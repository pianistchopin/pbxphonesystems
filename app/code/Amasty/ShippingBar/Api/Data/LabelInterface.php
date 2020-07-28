<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2019 Amasty (https://www.amasty.com)
 * @package Amasty_ShippingBar
 */


namespace Amasty\ShippingBar\Api\Data;

interface LabelInterface
{
    /**#@+
     * Constants defined for keys of data array
     */
    const LABEL_ID = 'label_id';
    const PROFILE_ID = 'profile_id';
    const STORE_ID = 'store_id';
    const ACTION = 'action';
    const LABEL = 'label';
    /**#@-*/

    /**#@+
     * Action Codes
     */
    const INIT_MESSAGE = 'init_message';
    const PROGRESS_MESSAGE = 'progress_message';
    const ACHIEVED_MESSAGE = 'achieved_message';
    const TERMS_MESSAGE = 'terms_message';
    /**#@-*/

    /**
     * @return mixed
     */
    public function getLabelId();

    /**
     * @param int $labelId
     *
     * @return LabelInterface
     */
    public function setLabelId($labelId);

    /**
     * @return int
     */
    public function getProfileId();

    /**
     * @param int $profileId
     *
     * @return LabelInterface
     */
    public function setProfileId($profileId);

    /**
     * @return int
     */
    public function getStoreId();

    /**
     * @param int $storeId
     *
     * @return LabelInterface
     */
    public function setStoreId($storeId);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     *
     * @return LabelInterface
     */
    public function setAction($action);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     *
     * @return LabelInterface
     */
    public function setLabel($label);
}
