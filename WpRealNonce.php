<?php

class WpRealNonce
{

    const OPT_PRE = 'wrn_';

    // User can use
    public function create($name): array
    {
        $id = self::gen_id();
        if (is_array($name)) {
            $name = trim($name['name']) ?? 'nonce';
        }
        $name = substr($name, 0, 20) . '_' . $id;

        $nonce = md5(wp_salt('nonce') . $name . microtime(true));
        $this->store($nonce, $name);

        return ["name" => $name, "value" => $nonce];
    }


    public function field($name = 'nonce'): string
    {
        if (is_array($name)) {
            $name = trim($name['name']) ?? 'nonce';
        }

        $name = $this->filter_string_polyfill($name);
        $nonce = $this->create($name);

        return '<input type="hidden" name="' . esc_attr($nonce['name']) . '" value="' . esc_attr($nonce['value']) . '" />';
    }


    public function check($name, $value): bool
    {
        if (empty($name) || empty($value)) {
            return false;
        }
        $name = $this->filter_string_polyfill($name);
        $nonce = $this->fetch($name);
        return ($nonce === $value);
    }


    public function store($nonce, $name): bool
    {
        if (empty($name)) return false;


        add_option(self::OPT_PRE . '_' . $name, $nonce);
        add_option(self::OPT_PRE . '_expires_' . $name, time() + 86400);

        return true;
    }

    public function delete($name): bool
    {
        $optionDeleted = delete_option(self::OPT_PRE . '_' . $name);
        $optionDeleted = $optionDeleted && delete_option(self::OPT_PRE . '_expires_' . $name);

        return (bool)$optionDeleted;
    }


    public function clear($force = false): int
    {
        if (defined('WP_SETUP_CONFIG') or defined('WP_INSTALLING')) {
            return 0;
        }

        global $wpdb;
        $exp = self::OPT_PRE . '_expires_%';;
        $rows = $wpdb->get_results("SELECT `option_id`, `option_name`, `option_value` FROM `{$wpdb->options}` WHERE option_name LIKE '{$exp}'");

        $deleted = 0;
        foreach ($rows as $single) {
            if ($force or ($single->option_value > time() + 86400)) {
                $name = substr($single->option_name, strlen(self::OPT_PRE . '_expires_'));
                $deleted += ($this->delete($name) ? 1 : 0);
            }
        }

        return (int)$deleted;
    }

    protected function fetch($name)
    {
        $returnValue = get_option(self::OPT_PRE . '_' . $name);
        $nonceExpires = get_option(self::OPT_PRE . '_expires_' . $name);

        $this->delete($name);

        if ($nonceExpires < time()) {
            $returnValue = null;
        }

        return $returnValue;
    }

    protected function gen_id(): string
    {
        require_once(ABSPATH . 'wp-includes/class-phpass.php');
        $hasher = new PasswordHash(8, false);

        return md5($hasher->get_random_bytes(100, false));
    }

    protected function filter_string_polyfill(string $string): string
    {
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);

        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }

}