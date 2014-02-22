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
 * YiiEmbedActiveFormModel
 *
 * @package yii-embed-wordpress
 */
class YiiEmbedActiveFormModel extends CFormModel
{

    /**
     * @var array
     */
    private $_attributes = array();

    /**
     * PHP getter magic method.
     * This method is overridden so that any attribute can be accessed.
     * @param string $name the property name or event name
     * @return mixed the property value, event handlers attached to the event, or the named behavior
     * @throws CException if the property or event is not defined
     * @see __set
     */
    public function __get($name)
    {
        try {
            return parent::__get($name);
        } catch (Exception $e) {
            if (isset($this->_attributes[$name]))
                return $this->_attributes[$name];
            if (isset($_POST['YdActiveFormModel'][$name]))
                return $this->_attributes[$name] = $_POST['YdActiveFormModel'][$name];
            return null;
        }
    }

    /**
     * PHP setter magic method.
     * This method is overridden so that any attribute can be accessed.
     * @param string $name property name
     * @param mixed $value property value
     * @return mixed|void
     * @throws CException
     */
    public function __set($name, $value)
    {
        try {
            parent::__set($name, $value);
        } catch (Exception $e) {
            $this->_attributes[$name] = $value;
            return;
        }
    }

    /**
     * Sets the attribute values in a massive way.
     * @param array $values attribute values (name=>value) to be set.
     * @param boolean $safeOnly whether the assignments should only be done to the safe attributes.
     * A safe attribute is one that is associated with a validation rule in the current {@link scenario}.
     * @see getSafeAttributeNames
     * @see attributeNames
     */
    public function setAttributes($values, $safeOnly = false)
    {
        if (!is_array($values))
            return;
        foreach ($values as $name => $value) {
            $this->_attributes[$name] = $value;
        }
    }

    /**
     * Returns all attribute values.
     * @param array $names list of attributes whose value needs to be returned.
     * Defaults to null, meaning all attributes as listed in {@link attributeNames} will be returned.
     * If it is an array, only the attributes in the array will be returned.
     * @return array attribute values (name=>value).
     */
    public function getAttributes($names = null)
    {
        $values = array();
        foreach (array_keys($this->_attributes) as $name)
            $values[$name] = $this->$name;

        if (is_array($names)) {
            $values2 = array();
            foreach ($names as $name)
                $values2[$name] = isset($values[$name]) ? $values[$name] : null;
            return $values2;
        }
        else
            return $values;
    }

}
