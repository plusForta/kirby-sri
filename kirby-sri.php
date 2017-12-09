<?php

/**
 * Kirby SRI - Subresource integrity hashing & cache-busting static assets for Kirby
 *
 * @package Kirby_Sri
 * @author  S1SYPHOS <hello@twobrain.io>
 * @license MIT <https://opensource.org/licenses/MIT>
 * @version GIT: 0.5.1
 * @link    http://twobrain.io
 *
 */

if (!c::get('plugin.kirby-sri')) {
    return;
}

class Settings
{

    /**
     * Returns the default options for `kirby-sri`
     *
     * @return array
     */

    public static function __callStatic($name, $args)
    {
        // Set prefix
        $prefix = 'plugin.kirby-sri.';
        // Set config names and fallbacks as settings
        $settings = [
            'algorithm'      => 'sha512', // Cryptographic hash algorithm
            'crossorigin'    => 'anonymous', // CORS settings attribute
            'fingerprinting' => true, // Enables / disables fingerprinting
        ];
        // If config settings exist, return the config with fallback
        if (isset($settings) && array_key_exists($name, $settings)) {
            return c::get($prefix . $name, $settings[$name]);
        }
    }
}

/**
 * Helper function generating base64-encoded SRI hashes
 *
 * @param  string $input
 * @return string
 */
function sri_checksum($input)
{
    $algorithm = settings::algorithm();
    $hash = hash($algorithm, $input, true);
    $hash_base64 = base64_encode($hash);

    return "$algorithm-$hash_base64";
}

/**
 * Returns true if we're currently running with browsersync active
 *
 * @return bool
 */
function isWebpack()
{
    return !!($_SERVER['HTTP_X_FORWARDED_FOR'] ?? null == 'webpack');
}


// Loading core
load(
    [
        's1syphos\\sri\\css' => __DIR__ . DS . 'core' . DS . 'css.php',
        's1syphos\\sri\\js'  => __DIR__ . DS . 'core' . DS . 'js.php',
    ]
);

// Registering with Kirby's extension registry
// if we're running under webpack, serve CSS as JS for hot-reload
kirby()->set('component', 'css', isWebpack() ? 'S1SYPHOS\\SRI\\JS' : 'S1SYPHOS\\SRI\\CSS');
kirby()->set('component', 'js', 'S1SYPHOS\\SRI\\JS');
