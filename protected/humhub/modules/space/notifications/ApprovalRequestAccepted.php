<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2016 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\space\notifications;

use Yii;
use yii\bootstrap\Html;
use humhub\modules\notification\components\BaseNotification;

/**
 * SpaceApprovalRequestAcceptedNotification
 *
 * @since 0.5
 */
class ApprovalRequestAccepted extends BaseNotification
{

    /**
     * @inheritdoc
     */
    public $moduleId = "space";

    /**
     * @inheritdoc
     */
    public function getAsHtml()
    {
        return Yii::t('SpaceModule.notification', '{displayName} approved your membership for the space {spaceName}', array(
                    '{displayName}' => Html::tag('strong', Html::encode($this->originator->displayName)),
                    '{spaceName}' => Html::tag('strong', Html::encode($this->source->name))
        ));
    }

}

?>