<?php
/**
 * Copyright (c) 2014, Mr PHP <info@mrphp.com.au>
 * All rights reserved.
 *  _____     _____ _____ _____
 * |     |___|  _  |  |  |  _  |
 * | | | |  _|   __|     |   __|
 * |_|_|_|_| |__|  |__|__|__|
 *
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this
 *   list of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice, this
 *   list of conditions and the following disclaimer in the documentation and/or
 *   other materials provided with the distribution.
 *
 * * Neither the name of the organization nor the names of its
 *   contributors may be used to endorse or promote products derived from
 *   this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON
 * ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */
Yii::import('zii.behaviors.CTimestampBehavior');

/**
 * YiiEmbedTimestampBehavior automatically detects the created and updated fields and populates them when the model is saved.
 *
 * @property CActiveRecord $owner
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedTimestampBehavior extends CTimestampBehavior
{

    /**
     * @var bool True to attempt to detect the fields to use for created and updated.
     */
    public $autoColumns = true;

    /**
     * @var bool
     */
    public $setUpdateOnCreate = true;

    /**
     * @var array Contains any fields that may be used to store the created timestamp.
     */
    public $createAttributes = array('created', 'create_time');

    /**
     * @var array Contains any fields that may be used to store the updated timestamp.
     */
    public $updateAttributes = array('updated', 'update_time');

    /**
     * Responds to {@link CModel::onBeforeSave} event.
     * Decides if there are created/updated fields then calls parent to update them.
     *
     * @param CModelEvent $event event parameter
     */
    public function beforeSave($event)
    {
        $this->_setAttributes();
        if ($this->owner->getIsNewRecord() && ($this->createAttribute !== null) && $this->owner->{$this->createAttribute} === null) {
            $this->owner->{$this->createAttribute} = $this->getTimestampByAttribute($this->createAttribute);
        }
        if ((!$this->owner->getIsNewRecord() || $this->setUpdateOnCreate) && ($this->updateAttribute !== null)) {
            $this->owner->{$this->updateAttribute} = $this->getTimestampByAttribute($this->updateAttribute);
        }
    }

    /**
     * Decides if there are created/updated fields and sets them to be used
     */
    private function _setAttributes()
    {
        if (!$this->autoColumns)
            return;
        $this->autoColumns = false;
        $this->createAttribute = $this->_getAttribute($this->createAttributes);
        $this->updateAttribute = $this->_getAttribute($this->updateAttributes);
    }

    /**
     * Checks the table to see if a matching field exists
     * @param array $attributes fields to check for
     * @return null|string
     */
    private function _getAttribute($attributes)
    {
        foreach ($attributes as $attribute)
            if (in_array($attribute, $this->owner->tableSchema->columnNames))
                return $attribute;
        return null;
    }

    /**
     * Returns the appropriate timestamp depending on $columnType
     *
     * @param string $columnType $columnType
     * @return mixed timestamp (eg unix timestamp or a mysql function)
     */
    protected function getTimestampByColumnType($columnType)
    {
        if ($columnType == 'datetime')
            return date('Y-m-d H:i:s');
        if ($columnType == 'timestamp')
            return date('Y-m-d H:i:s');
        if ($columnType == 'date')
            return date('Y-m-d');
        return time();
    }

}
