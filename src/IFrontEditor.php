<?php declare(strict_types=1);

namespace FrontEditor;

use GeneralForm\IFormContainer;
use GeneralForm\ITemplatePath;


/**
 * Interface IFrontEditor
 *
 * @author  geniv
 * @package FrontEditor
 */
interface IFrontEditor extends ITemplatePath
{

    /**
     * Get form container.
     *
     * @return IFormContainer
     */
    public function getFormContainer(): IFormContainer;


    /**
     * Set template path link.
     *
     * @param string $path
     */
    public function setTemplatePathLink(string $path);


    /**
     * Set admin link.
     *
     * @param string|null $adminLink
     */
    public function setAdminLink(string $adminLink = null);


    /**
     * Add variable template.
     *
     * @param string $name
     * @param        $values
     */
    public function addVariableTemplate(string $name, $values);


    /**
     * Set ACL.
     *
     * @param bool $acl
     */
    public function setAcl(bool $acl);


    /**
     * Set data.
     *
     * @param array $data
     */
    public function setData(array $data);
}
