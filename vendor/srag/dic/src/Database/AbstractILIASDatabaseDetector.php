<?php

namespace srag\DIC\LiveVoting\Database;

use ilDBConstants;
use ilDBPdoInterface;

/**
 * Class AbstractILIASDatabaseDetector
 *
 * @package srag\DIC\LiveVoting\Database
 */
abstract class AbstractILIASDatabaseDetector implements DatabaseInterface
{

    /**
     * @var ilDBPdoInterface
     */
    protected $db;


    /**
     * AbstractILIASDatabaseDetector constructor
     *
     * @param ilDBPdoInterface $db
     */
    public function __construct(ilDBPdoInterface $db)
    {
        $this->db = $db;
    }


    /**
     * @inheritDoc
     */
    static function getReservedWords()
    {
        // TODO
        return [];
    }


    /**
     * @inheritDoc
     */
    public static function isReservedWord($a_word)
    {
        // TODO
        return false;
    }


    /**
     * @inheritDoc
     */
    public function addFulltextIndex($table_name, $afields, $a_name = 'in')
    {
        return $this->db->addFulltextIndex($a_name, $afields, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function addIndex($table_name, $fields, $index_name = '', $fulltext = false)
    {
        return $this->db->addIndex($table_name, $fields, $index_name, $fulltext);
    }


    /**
     * @inheritDoc
     */
    public function addPrimaryKey($table_name, $primary_keys)
    {
        $this->db->addPrimaryKey($table_name, $primary_keys);
    }


    /**
     * @inheritDoc
     */
    public function addTableColumn($table_name, $column_name, $attributes)
    {
        $this->db->addTableColumn($table_name, $column_name, $attributes);
    }


    /**
     * @inheritDoc
     */
    public function addUniqueConstraint($table, $fields, $name = "con")
    {
        return $this->db->addUniqueConstraint($table, $fields, $name);
    }


    /**
     * @inheritDoc
     */
    public function autoExecute($tablename, $fields, $mode = ilDBConstants::AUTOQUERY_INSERT, $where = false)
    {
        return $this->db->autoExecute($tablename, $fields, $mode, $where);
    }


    /**
     * @inheritDoc
     */
    public function beginTransaction()
    {
        return $this->db->beginTransaction();
    }


    /**
     * @inheritDoc
     */
    public function buildAtomQuery()
    {
        return $this->db->buildAtomQuery();
    }


    /**
     * @inheritDoc
     */
    public function cast($a_field_name, $a_dest_type)
    {
        return $this->db->cast($a_field_name, $a_dest_type);
    }


    /**
     * @inheritDoc
     */
    public function checkIndexName($name)
    {
        return $this->db->checkIndexName($name);
    }


    /**
     * @inheritDoc
     */
    public function checkTableName($a_name)
    {
        return $this->db->checkTableName($a_name);
    }


    /**
     * @inheritDoc
     */
    public function commit()
    {
        return $this->db->commit();
    }


    /**
     * @inheritDoc
     */
    public function concat(array $values, $allow_null = true)
    {
        return $this->db->concat($values, $allow_null);
    }


    /**
     * @inheritDoc
     */
    public function connect($return_false_on_error = false)
    {
        return $this->connect($return_false_on_error);
    }


    /**
     * @inheritDoc
     */
    public function constraintName($a_table, $a_constraint)
    {
        return $this->db->constraintName($a_table, $a_constraint);
    }


    /**
     * @inheritDoc
     */
    public function createDatabase($a_name, $a_charset = "utf8", $a_collation = "")
    {
        return $this->db->createDatabase($a_name, $a_charset, $a_collation);
    }


    /**
     * @inheritDoc
     */
    public function createSequence($table_name, $start = 1)
    {
        $this->db->createSequence($table_name, $start);
    }


    /**
     * @inheritDoc
     */
    public function createTable($table_name, $fields, $drop_table = false, $ignore_erros = false)
    {
        return $this->db->createTable($table_name, $fields, $drop_table, $ignore_erros);
    }


    /**
     * @inheritDoc
     */
    public function doesCollationSupportMB4Strings()
    {
        return $this->db->doesCollationSupportMB4Strings();
    }


    /**
     * @inheritDoc
     */
    public function dropFulltextIndex($a_table, $a_name)
    {
        return $this->db->dropFulltextIndex($a_table, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function dropIndex($a_table, $a_name = "i1")
    {
        return $this->db->dropIndex($a_table, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function dropIndexByFields($table_name, $afields)
    {
        return $this->db->dropIndexByFields($table_name, $afields);
    }


    /**
     * @inheritDoc
     */
    public function dropPrimaryKey($table_name)
    {
        $this->db->dropPrimaryKey($table_name);
    }


    /**
     * @param $table_name string
     */
    public function dropSequence($table_name)
    {
        $this->db->dropSequence($table_name);
    }


    /**
     * @inheritDoc
     */
    public function dropTable($table_name, $error_if_not_existing = true)
    {
        return $this->db->dropTable($table_name, $error_if_not_existing);
    }


    /**
     * @inheritDoc
     */
    public function dropTableColumn($table_name, $column_name)
    {
        $this->db->dropTableColumn($table_name, $column_name);
    }


    /**
     * @inheritDoc
     */
    public function dropUniqueConstraint($table, $name = "con")
    {
        return $this->db->dropUniqueConstraint($table, $name);
    }


    /**
     * @inheritDoc
     */
    public function dropUniqueConstraintByFields($table, $fields)
    {
        return $this->db->dropUniqueConstraintByFields($table, $fields);
    }


    /**
     * @inheritDoc
     */
    public function enableResultBuffering($a_status)
    {
        $this->db->enableResultBuffering($a_status);
    }


    /**
     * @inheritDoc
     */
    public function equals($columns, $value, $type, $emptyOrNull = false)
    {
        return $this->db->equals($columns, $value, $type, $emptyOrNull);
    }


    /**
     * @inheritDoc
     */
    public function escape($value, $escape_wildcards = false)
    {
        return $this->db->escape($value, $escape_wildcards);
    }


    /**
     * @inheritDoc
     */
    public function escapePattern($text)
    {
        return $this->db->escapePattern($text);
    }


    /**
     * @inheritDoc
     */
    public function execute($stmt, $data = array())
    {
        return $this->db->execute($stmt, $data);
    }


    /**
     * @inheritDoc
     */
    public function executeMultiple($stmt, $data)
    {
        $this->db->executeMultiple($stmt, $data);
    }


    /**
     * @inheritDoc
     */
    public function fetchAll($query_result, $fetch_mode = ilDBConstants::FETCHMODE_ASSOC)
    {
        return $this->db->fetchAll($query_result, $fetch_mode = ilDBConstants::FETCHMODE_ASSOC);
    }


    /**
     * @inheritDoc
     */
    public function fetchAssoc($query_result)
    {
        return $this->db->fetchAssoc($query_result);
    }


    /**
     * @inheritDoc
     */
    public function fetchObject($query_result)
    {
        return $this->db->fetchObject($query_result);
    }


    /**
     * @inheritDoc
     */
    public function free($a_st)
    {
        return $this->db->free($a_st);
    }


    /**
     * @inheritDoc
     */
    public function fromUnixtime($expr, $to_text = true)
    {
        return $this->db->fromUnixtime($expr, $to_text);
    }


    /**
     * @inheritDoc
     */
    public function getAllowedAttributes()
    {
        return $this->db->getAllowedAttributes();
    }


    /**
     * @inheritDoc
     */
    public function getDBType()
    {
        return $this->db->getDBType();
    }


    /**
     * @inheritDoc
     */
    public function getDBVersion()
    {
        return $this->db->getDBVersion();
    }


    /**
     * @inheritDoc
     */
    public function getDSN()
    {
        return $this->db->getDSN();
    }


    /**
     * @inheritDoc
     */
    public function getLastInsertId()
    {
        return $this->db->getLastInsertId();
    }


    /**
     * @inheritDoc
     */
    public function getPrimaryKeyIdentifier()
    {
        return $this->db->getPrimaryKeyIdentifier();
    }


    /**
     * @inheritDoc
     */
    public function getSequenceName($table_name)
    {
        return $this->db->getSequenceName($table_name);
    }


    /**
     * @inheritDoc
     */
    public function getServerVersion($native = false)
    {
        return $this->db->getServerVersion($native);
    }


    /**
     * @inheritDoc
     */
    public function getStorageEngine()
    {
        return $this->db->getStorageEngine();
    }


    /**
     * @inheritDoc
     */
    public function groupConcat($a_field_name, $a_seperator = ",", $a_order = null)
    {
        return $this->db->groupConcat($a_field_name, $a_seperator, $a_order);
    }


    /**
     * @inheritDoc
     */
    public function in($field, $values, $negate = false, $type = "")
    {
        return $this->db->in($field, $values, $negate, $type);
    }


    /**
     * @inheritDoc
     */
    public function indexExistsByFields($table_name, $fields)
    {
        return $this->db->indexExistsByFields($table_name, $fields);
    }


    /**
     * @inheritDoc
     */
    public function initFromIniFile($tmpClientIniFile = null)
    {
        $this->db->initFromIniFile($tmpClientIniFile);
    }


    /**
     * @inheritDoc
     */
    public function insert($table_name, $values)
    {
        return $this->db->insert($table_name, $values);
    }


    /**
     * @inheritDoc
     */
    public function isFulltextIndex($a_table, $a_name)
    {
        return $this->db->isFulltextIndex($a_table, $a_name);
    }


    /**
     * @inheritDoc
     */
    public function like($column, $type, $value = "?", $case_insensitive = true)
    {
        return $this->db->like($column, $type, $value, $case_insensitive);
    }


    /**
     * @inheritDoc
     */
    public function listSequences()
    {
        return $this->db->listSequences();
    }


    /**
     * @inheritDoc
     */
    public function listTables()
    {
        return $this->db->listTables();
    }


    /**
     * @inheritDoc
     *
     * @internal
     */
    public function loadModule($module)
    {
        return $this->db->loadModule($module);
    }


    /**
     * @inheritDoc
     */
    public function locate($a_needle, $a_string, $a_start_pos = 1)
    {
        return $this->db->locate($a_needle, $a_string, $a_start_pos);
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function lockTables($tables)
    {
        $this->db->lockTables($tables);
    }


    /**
     * @inheritDoc
     */
    public function lower($a_exp)
    {
        return $this->db->lower($a_exp);
    }


    /**
     * @inheritDoc
     */
    public function manipulate($query)
    {
        return $this->db->manipulate($query);
    }


    /**
     * @inheritDoc
     */
    public function manipulateF($query, $types, $values)
    {
        return $this->db->manipulateF($query, $types, $values);
    }


    /**
     * @inheritDoc
     */
    public function migrateAllTablesToCollation($collation = ilDBConstants::MYSQL_COLLATION_UTF8MB4)
    {
        return $this->db->migrateAllTablesToCollation($collation);
    }


    /**
     * @inheritDoc
     */
    public function migrateAllTablesToEngine($engine = ilDBConstants::MYSQL_ENGINE_INNODB)
    {
        return $this->db->migrateAllTablesToEngine($engine);
    }


    /**
     * @inheritDoc
     */
    public function modifyTableColumn($table, $column, $attributes)
    {
        return $this->db->modifyTableColumn($table, $column, $attributes);
    }


    /**
     * @inheritDoc
     */
    public function nextId($table_name)
    {
        return $this->db->nextId($table_name);
    }


    /**
     * @inheritDoc
     */
    public function now()
    {
        return $this->db->now();
    }


    /**
     * @inheritDoc
     */
    public function numRows($query_result)
    {
        return $this->db->numRows($query_result);
    }


    /**
     * @inheritDoc
     */
    public function prepare($a_query, $a_types = null, $a_result_types = null)
    {
        return $this->db->prepare($a_query, $a_types, $a_result_types);
    }


    /**
     * @inheritDoc
     */
    public function prepareManip($a_query, $a_types = null)
    {
        return $this->db->prepareManip($a_query, $a_types);
    }


    /**
     * @inheritDoc
     */
    public function query($query)
    {
        return $this->db->query($query);
    }


    /**
     * @inheritDoc
     */
    public function queryCol($query, $type = ilDBConstants::FETCHMODE_DEFAULT, $colnum = 0)
    {
        return $this->db->queryCol($query, $type, $colnum);
    }


    /**
     * @inheritDoc
     */
    public function queryF($query, $types, $values)
    {
        return $this->db->queryF($query, $types, $values);
    }


    /**
     * @inheritDoc
     */
    public function queryRow($query, $types = null, $fetchmode = ilDBConstants::FETCHMODE_DEFAULT)
    {
        return $this->db->queryRow($query, $types, $fetchmode);
    }


    /**
     * @inheritDoc
     */
    public function quote($value, $type)
    {
        return $this->db->quote($value, $type);
    }


    /**
     * @inheritDoc
     */
    public function quoteIdentifier($identifier, $check_option = false)
    {
        return $this->db->quoteIdentifier($identifier, $check_option);
    }


    /**
     * @param $old_name
     * @param $new_name
     *
     * @return mixed
     */
    public function renameTable($old_name, $new_name)
    {
        return $this->db->renameTable($old_name, $new_name);
    }


    /**
     * @inheritDoc
     */
    public function renameTableColumn($table_name, $column_old_name, $column_new_name)
    {
        $this->db->renameTableColumn($table_name, $column_old_name, $column_new_name);
    }


    /**
     * @inheritDoc
     */
    public function replace($table, $primaryKeys, $otherColumns)
    {
        $this->db->replace($table, $primaryKeys, $otherColumns);
    }


    /**
     * @inheritDoc
     */
    public function rollback()
    {
        return $this->db->rollback();
    }


    /**
     * @inheritDoc
     */
    public function sanitizeMB4StringIfNotSupported($query)
    {
        return $this->db->sanitizeMB4StringIfNotSupported($query);
    }


    /**
     * @inheritDoc
     */
    public function sequenceExists($sequence)
    {
        return $this->db->sequenceExists($sequence);
    }


    /**
     * @inheritDoc
     */
    public function setDBHost($host)
    {
        $this->db->setDBHost($host);
    }


    /**
     * @inheritDoc
     */
    public function setDBPassword($password)
    {
        $this->db->setDBPassword($password);
    }


    /**
     * @inheritDoc
     */
    public function setDBPort($port)
    {
        $this->db->setDBPort($port);
    }


    /**
     * @inheritDoc
     */
    public function setDBUser($user)
    {
        $this->db->setDBUser($user);
    }


    /**
     * @inheritDoc
     */
    public function setLimit($limit, $offset)
    {
        $this->db->setLimit($limit, $offset);
    }


    /**
     * @inheritDoc
     */
    public function setStorageEngine($storage_engine)
    {
        $this->db->setStorageEngine($storage_engine);
    }


    /**
     * @inheritDoc
     */
    public function substr($a_exp)
    {
        return $this->db->substr($a_exp);
    }


    /**
     * @inheritDoc
     */
    public function supports($feature)
    {
        return $this->db->supports($feature);
    }


    /**
     * @inheritDoc
     */
    public function supportsCollationMigration()
    {
        return $this->db->supportsCollationMigration();
    }


    /**
     * @inheritDoc
     */
    public function supportsEngineMigration()
    {
        return $this->db->supportsEngineMigration();
    }


    /**
     * @inheritDoc
     */
    public function supportsFulltext()
    {
        return $this->db->supportsFulltext();
    }


    /**
     * @inheritDoc
     */
    public function supportsSlave()
    {
        return $this->db->supportsSlave();
    }


    /**
     * @inheritDoc
     */
    public function supportsTransactions()
    {
        return $this->db->supportsTransactions();
    }


    /**
     * @inheritDoc
     */
    public function tableColumnExists($table_name, $column_name)
    {
        return $this->db->tableColumnExists($table_name, $column_name);
    }


    /**
     * @inheritDoc
     */
    public function tableExists($table_name)
    {
        return $this->db->tableExists($table_name);
    }


    /**
     * @inheritDoc
     */
    public function uniqueConstraintExists($table, array $fields)
    {
        return $this->db->uniqueConstraintExists($table, $fields);
    }


    /**
     * @return string
     *
     * @deprecated
     */
    public function unixTimestamp()
    {
        return $this->db->unixTimestamp();
    }


    /**
     * @inheritDoc
     *
     * @deprecated
     */
    public function unlockTables()
    {
        $this->db->unlockTables();
    }


    /**
     * @inheritDoc
     */
    public function update($table_name, $values, $where)
    {
        return $this->db->update($table_name, $values, $where);
    }


    /**
     * @inheritDoc
     */
    public function upper($a_exp)
    {
        return $this->db->upper($a_exp);
    }


    /**
     * @inheritDoc
     */
    public function useSlave($bool)
    {
        return $this->db->useSlave($bool);
    }
}
