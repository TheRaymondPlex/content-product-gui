{%- set netif = pillar.network.project_interface -%}
{%- set couchbase_servers = salt['mine.get']('roles:couchbase', 'network.interfaces', expr_form = 'grain').items() -%}
<?php
/**
 * !!! This file is maintained by salt. Do not modify this file, as the changes will be overwritten!
 *
 */

/** Database: ZED operations */
$config['db'] = [
    'username' => '{{ environment_details.database.zed.username }}',
    'password' => '{{ environment_details.database.zed.password }}',
    'database' => '{{ store }}_{{ environment}}_zed',
    'host'     => '{{ environment_details.database.zed.hostname }}',
];

/** Database: ZED dump */
$config['db_dump'] = [
    'username' => '{{ environment_details.database.zed.username }}',
    'password' => '{{ environment_details.database.zed.password }}',
    'database' => '{{ store }}_{{ environment}}_dump',
    'host'     => '{{ environment_details.database.zed.hostname }}',
    'mysqldump_bin' => '/usr/bin/mysqldump',
    'mysql_bin'     => '/usr/bin/mysql',
];

/** Memcache as KeyValue store */
$config['storage']['kv'] = [
    'source' => 'couchbase',
    'couchbase' => [
        'hosts' => [
{%- for hostname, network_settings in couchbase_servers %}
            [
                'host' => '{{ network_settings[netif]['inet'][0]['address'] }}',
                'port' => '{{ pillar.couchbase.port }}',
            ],
{% endfor %}
        ],
        'user'   => '{{ store }}_{{ environment }}_yves',
        'password' => '{{ pillar.couchbase.password }}',
        'bucket' => '{{ store }}_{{ environment }}_yves',
        'timeout' => 0
    ]
];


/** Public URL's */
$config['host'] = $config['host_ssl'] = [
    'zed_gui' => '{{ environment_details.stores[store].zed.hostname }}',
    'zed_api' => 'localhost:{{ environment_details.stores[store].zed.port }}', 
    'yves' => '{{ environment_details.stores[store].yves.hostnames[0] }}',
    'static_assets' => '{{ environment_details.static.hostname }}',
    'static_media' => '{{ environment_details.static.hostname }}',
];

/** Session storage */
$config['zed']['session']['save_handler'] = 'couchbase';
$config['zed']['session']['save_path'] = '{%- for hostname, network_settings in couchbase_servers -%}
{{store}}_{{environment}}_sessions:{{ pillar.couchbase.password }}@{{ network_settings[netif]['inet'][0]['address'] }}:{{ pillar.couchbase.port }}{% if not loop.last %};{% endif -%}{% endfor %}';

$config['yves']['session'] = $config['zed']['session'];

// Fixme
/** Payment gateways */
/** Logistic partners */
/** MCM, tracking */
/** Facebook / external auth providers */
