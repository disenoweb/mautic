<?php
/**
 * @package     Mautic
 * @copyright   2014 Mautic Contributors. All rights reserved.
 * @author      Mautic
 * @link        http://mautic.org
 * @license     GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

namespace Mautic\FormBundle\Model;

use Mautic\CoreBundle\Helper\InputHelper;
use Mautic\CoreBundle\Model\FormModel as CommonFormModel;
use Mautic\FormBundle\Entity\Field;

/**
 * Class FieldModel
 */
class FieldModel extends CommonFormModel
{

    /**
     * {@inheritdoc}
     *
     * @return \Mautic\FormBundle\Entity\FieldRepository
     */
    public function getRepository()
    {
        return $this->em->getRepository('MauticFormBundle:Field');
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissionBase()
    {
        return 'form:forms';
    }

    /**
     * {@inheritdoc}
     */
    public function getEntity($id = null)
    {
        if ($id === null) {
            return new Field();
        }

        return parent::getEntity($id);
    }

    /**
     * Get the fields saved in session
     *
     * @return array
     */
    public function getSessionFields($formId)
    {
        $session = $this->factory->getSession();
        $fields = $session->get('mautic.form.'.$formId.'.fields.modified', array());
        $remove = $session->get('mautic.form.'.$formId.'.fields.deleted', array());
        return array_diff_key($fields, array_flip($remove));
    }

    /**
     * @param $formId
     * @param $label
     */
    public function generateAlias($label, &$aliases)
    {
        // Some labels are quite long if a question so cut this short
        $alias = substr(strtolower(InputHelper::alphanum($label, false, '_')), 0, 25);

        if (substr($alias, -1) == '_') {
            $alias = substr($alias, 0, -1);
        }

        //make sure alias is not already taken
        $testAlias = $alias;

        $count     = (int) in_array($alias, $aliases);
        $aliasTag  = $count;

        while ($count) {
            $testAlias = $alias . $aliasTag;
            $count     = (int) in_array($testAlias, $aliases);
            $aliasTag++;
        }

        $aliases[] = $testAlias;

        return $testAlias;
    }
}
