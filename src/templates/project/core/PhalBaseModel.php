<?php


namespace @@namespace@@\Core;


use Phalcon\Db\Adapter\Pdo;
use Phalcon\Mvc\Model;

/**
 * Class PhalBaseModel
 * @package App\Core
 */
class PhalBaseModel extends Model
{
    /** @var PhalBaseLogger */
    protected $logger;
    /**
     * onController method for model
     */
    public function onConstruct()
    {
        $this->logger = $this->getDI()->get('logger');
    }

    /**
     * @param array $data 要插入的数据，键为字段名，值 为字段值
     * @param string|null $db
     * @return int|false
     */
    public function batch_insert(array $data, string $db = null)
    {
        if (!is_array($data) || count($data) == 0) {
            return false;
        }

        $keys = array_keys(reset($data));
        $keys = array_map(function ($key) {
            return "`{$key}`";
        }, $keys);

        $keys = implode(',', $keys);
        $sql = "INSERT INTO " . $this->getSource() . " ({$keys}) VALUES ";

        foreach ($data as $v) {
            $v = array_map(function ($value) {
                if ($value === null) {
                    return 'NULL';
                } else {
                    $value = addslashes($value); //处理特殊符号，如单引号
                    return "'{$value}'";
                }
            }, $v);

            $values = implode(',', array_values($v));
            $sql .= " ({$values}), ";
        }

        $sql = rtrim(trim($sql), ',');
        /** @var Pdo $db */
        $db = $this->getDI()->get($db);
        try {
            $db->execute($sql);
            $ret = $db->affectedRows();
            return $ret;
        } catch (\Exception $e) {
            $this->logger->write_log($e->getMessage(), 'error');
            return false;
        }
    }
}