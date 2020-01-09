<?php

use yii\db\Migration;

/**
 * Class m200109_132144_rss
 */
class m200109_132144_rss extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        if (class_exists('\wdmg\pages\models\Pages')) {
            $userTable = \wdmg\pages\models\Pages::tableName();

            if (is_null($this->getDb()->getSchema()->getTableSchema($userTable)->getColumn('in_rss')))
                $this->addColumn($userTable, 'in_rss', $this->boolean()->defaultValue(true));

        }

        if (class_exists('\wdmg\news\models\News')) {
            $userTable = \wdmg\news\models\News::tableName();

            if (is_null($this->getDb()->getSchema()->getTableSchema($userTable)->getColumn('in_rss')))
                $this->addColumn($userTable, 'in_rss', $this->boolean()->defaultValue(true));

        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        if (class_exists('\wdmg\pages\models\Pages')) {
            $userTable = \wdmg\pages\models\Pages::tableName();

            if (!is_null($this->getDb()->getSchema()->getTableSchema($userTable)->getColumn('in_rss')))
                $this->dropColumn($userTable, 'in_rss');

        }

        if (class_exists('\wdmg\news\models\News')) {
            $userTable = \wdmg\news\models\News::tableName();

            if (!is_null($this->getDb()->getSchema()->getTableSchema($userTable)->getColumn('in_rss')))
                $this->dropColumn($userTable, 'in_rss');

        }

    }
}
