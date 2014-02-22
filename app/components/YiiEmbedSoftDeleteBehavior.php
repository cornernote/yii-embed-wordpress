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

/**
 * YiiEmbedSoftDeleteBehavior automatically sets a deleted field to the date instead of deleting the row from the database.
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedSoftDeleteBehavior extends CActiveRecordBehavior
{
    /**
     * The field to use to store the deleted date.
     * @var string
     */
    public $deleted = 'deleted';

    /**
     * Override the default delete to update the deleted field instead of deleting the row from the database.
     * @param CModelEvent $event
     */
    public function beforeDelete($event)
    {
        if ($this->deleted && isset($this->owner->tableSchema->columns[$this->deleted])) {
            $this->owner->{$this->deleted} = date('Y-m-d H:i:s');
        }
        $this->owner->save(false);

        //prevent real deletion
        $event->isValid = false;
    }

    /**
     * Method available to the model to perform an undelete.
     * @return mixed
     * @throws CDbException
     */
    public function undelete()
    {
        if (!$this->owner->isNewRecord) {
            Yii::trace(get_class($this) . '.undelete()', 'system.db.ar.CActiveRecord');
            $updateFields = array(
                $this->deleted => null,
            );
            return $this->owner->updateByPk($this->owner->getPrimaryKey(), $updateFields);
        }
        else
            throw new CDbException(Yii::t('yii', 'The active record cannot be undeleted because it is new.'));
    }

    /**
     * Method available to the model to help finding deleted records.
     *
     * eg:
     * <pre>
     * Model::model()->deleteds()->findAll();
     * </pre>
     * @return mixed
     */
    public function deleteds()
    {
        $this->owner->dbCriteria->mergeWith(array(
            'condition' => $this->deleted . ' IS NOT NULL'
        ));
        return $this->owner;
    }

    /**
     * Method available to the model to help excluding deleted records from the results.
     *
     * eg:
     * <pre>
     * Model::model()->notDeleteds()->findAll();
     * </pre>
     * @return mixed
     */
    public function notDeleteds()
    {
        $this->owner->dbCriteria->mergeWith(array(
            'condition' => $this->deleted . ' IS NULL'
        ));
        return $this->owner;
    }

}