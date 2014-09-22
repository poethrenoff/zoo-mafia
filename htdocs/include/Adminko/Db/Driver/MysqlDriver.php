<?php
namespace Adminko\Db\Driver;

use PDO;
use Adminko\Metadata;

class MysqlDriver extends Driver
{
    protected function __construct($db_type, $db_host, $db_port, $db_name, $db_user, $db_password)
    {
        $this->dbh = new PDO("{$db_type}:host={$db_host};dbname={$db_name}" . ($db_port ? ";port={$db_port}" : ""), $db_user, $db_password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

        $this->query("set names 'utf8' collate 'utf8_general_ci'");
    }

    public function create()
    {
        $sql = "<pre>\n";

        foreach (Metadata::$objects as $object_name => $object_desc) {
            if (!(isset($object_desc['fields']) && $object_desc['fields'])) {
                continue;
            }

            $sql .= "drop table if exists {$object_name};\n";
            $sql .= "create table {$object_name} (\n";

            $fields = array();
            $pk_field = '';
            foreach ($object_desc['fields'] as $field_name => $field_desc) {
                switch ($field_desc['type']) {
                    case 'pk': $type = "int(11) not null auto_increment";
                        $pk_field = $field_name;
                        break;
                    case 'string': case 'select': case 'image': case 'file': case 'password':
                        $type = "varchar(255) not null default ''";
                        break;
                    case 'date': case 'datetime': $type = "varchar(14) not null default ''";
                        break;
                    case 'text': $type = "text";
                        break;
                    case 'int': $type = "int(11) default '0'";
                        break;
                    case 'float': $type = "double default '0'";
                        break;
                    case 'active': case 'boolean': case 'order':
                    case 'default': case 'table': case 'parent':
                        $type = "int(11) not null default '0'";
                        break;
                    default: $type = "error";
                }
                $fields[] = "\t{$field_name} {$type}";
            }
            if ($pk_field) {
                $fields[] = "\tprimary key ({$pk_field})";
            }

            $sql .= join(",\n", $fields) . "\n";
            $sql .= ");\n";

            $index_count = 1;
            foreach ($object_desc['fields'] as $field_name => $field_desc) {
                switch ($field_desc['type']) {
                    case 'select': case 'table': case 'active': case 'parent': case 'order':
                        $sql .= "create index {$object_name}_idx" . $index_count++ . "\n\ton {$object_name} ({$field_name});\n";
                }
            }

            $sql .= "\n";
        }

        $sql .= "</pre>\n";

        print $sql;

        exit;
    }
}
