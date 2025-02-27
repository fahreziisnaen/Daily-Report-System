<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = Settings::all()->groupBy('group');
        $ldapEnabled = filter_var(Settings::get('ldap_enabled', 'false'), FILTER_VALIDATE_BOOLEAN);
        $ldapHost = Settings::get('ldap_host');
        $ldapPort = Settings::get('ldap_port', 389);
        $ldapBaseDn = Settings::get('ldap_base_dn');
        $ldapUsername = Settings::get('ldap_username');
        $ldapPassword = Settings::get('ldap_password');

        return view('admin.settings.index', compact(
            'settings',
            'ldapEnabled',
            'ldapHost',
            'ldapPort',
            'ldapBaseDn',
            'ldapUsername',
            'ldapPassword'
        ));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'ldap_enabled' => 'boolean',
            'ldap_host' => 'required_if:ldap_enabled,1',
            'ldap_port' => 'required_if:ldap_enabled,1|numeric',
            'ldap_base_dn' => 'required_if:ldap_enabled,1',
            'ldap_username' => 'required_if:ldap_enabled,1',
            'ldap_password' => 'required_if:ldap_enabled,1',
        ]);

        // Convert boolean to string for storage
        $validated['ldap_enabled'] = $validated['ldap_enabled'] ? 'true' : 'false';

        foreach ($validated as $key => $value) {
            Settings::set($key, $value);
        }

        Cache::forget('settings');

        return redirect()->back()->with('success', 'Settings updated successfully');
    }

    public function testLdapConnection(Request $request)
    {
        try {
            // Implement LDAP connection test here
            $connection = ldap_connect($request->ldap_host, $request->ldap_port);
            
            if ($connection) {
                ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
                ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
                
                $bind = ldap_bind($connection, $request->ldap_username, $request->ldap_password);
                
                if ($bind) {
                    return response()->json(['success' => true, 'message' => 'LDAP connection successful']);
                }
            }
            
            return response()->json(['success' => false, 'message' => 'LDAP connection failed']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
} 