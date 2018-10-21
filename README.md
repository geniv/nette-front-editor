Front editor
============

Installation
------------

```sh
$ composer require geniv/nette-front-editor
```
or
```json
"geniv/nette-front-editor": ">=1.0.0"
```

require:
```json
"php": ">=7.0.0",
"nette/nette": ">=2.4.0",
"geniv/nette-general-form": ">=1.0.0",
"geniv/nette-identity": ">=1.0.0"
```

Include in application
----------------------

neon configure:
```neon
# front editor
frontEditor:
#   autowired: true
#   formContainer: FrontEditor\FormContainer
#   adminLink: "admin/%routerPrefix.adminBaseUrl%"
```

neon configure extension:
```neon
extensions:
    frontEditor: FrontEditor\Bridges\Nette\Extension
```

front editor v.1:
-----------------

presenters:
```php
use FrontEditorControl;

//$this->template->frontEditorEnable = $this->isFrontEditorEnable();
//$frontEditor->setAcl($this->isFrontEditorEnable());
//$frontEditor->getFrontEditorEnableHash();

//<a n:href="FrontEditorDisable!" n:if="$frontEditorEnable" class="ajax">odhlasi se z edit modu</a>
//{control frontEditor.'-identText1'}

protected function createComponentFrontEditor(FrontEditor $frontEditor): FrontEditor
{
    $frontEditor->setTemplatePath(__DIR__ . '/templates/frontEditor.latte');
    $frontEditor->setAcl($this->user->isAllowed($this->getName(), 'edit'));
    $frontEditor->setData($this['config']->getEditor(static::IDENTIFIER));
    $frontEditor->onSuccess[] = function (Form $form, array $values) {
        try {
            if ($this['config']->setEditor(static::IDENTIFIER, $values['content'])) {
                $this->flashMessage($this->translator->translate('front-editor-onsuccess'), 'success');
            }
        } catch (\Dibi\Exception $e) {
            $this->flashMessage($e->getMessage(), 'danger');
        }
        $this->redirect('this');
    };
    return $frontEditor;
}
```

usage:
```latte
{control frontEditor}
```

front editor v.2:
-----------------

presenters front:
```php
use FrontEditorControl;

protected function startup()
{
    parent::startup();

    $this->template->frontEditorEnable = $this->isFrontEditorEnable();
}

protected function createComponentFrontEditor(FrontEditor $frontEditor): Multiplier
{
    $frontEditor->setTemplatePath(__DIR__ . '/templates/frontEditor.latte');
    $frontEditor->setAcl($this->isFrontEditorEnable());

    return new Multiplier(function ($indexName) use ($frontEditor) {
        $data = $this['config']->getDataByIdent($indexName);
        if (!$data) {
            $this['config']->setEditor($indexName, $indexName); // create if not exists
            return $frontEditor;
        }
        // set type and add variable to frontEditor
        $frontEditor->getFormContainer()->setType($data['type']);
        $frontEditor->addVariableTemplate('type', $data['type']);

        $frontEditor->setData($data['content']);
        $frontEditor->onSuccess[] = function (Form $form, array $values) use ($data) {
            try {
                if ($this['config']->editData($data['id'], ['content' => $values['content']])) {
                    $this->flashMessage('done', 'success');
                }
            } catch (\Dibi\Exception $e) {
                $this->flashMessage($e->getMessage(), 'danger');
            }
            $this->redirect('this');
        };
        return clone $frontEditor;
    });
}
```

usage front:
```latte
<a n:href="FrontEditorDisable!" n:if="$frontEditorEnable" class="ajax">logout edit mode</a>

{control frontEditor.'-identText1'}
```

front editor v.3:
-----------------

presenters front:
```php
use FrontEditorControl;

protected function startup()
{
    parent::startup();

    $this->template->frontEditorEnable = $this->isFrontEditorEnable();
}

protected function createComponentFrontEditor(FrontEditor $frontEditor): FrontEditor
{
    $frontEditor->setTemplatePath(__DIR__ . '/templates/frontEditor.latte');
    $frontEditor->setTemplatePathLink(__DIR__ . '/templates/frontEditorLink.latte');
    $frontEditor->setAcl($this->isFrontEditorEnable());

    $frontEditor->onLoadData = function ($identification) use ($frontEditor) {
        if ($identification) {
            $data = $this['config']->getDataByIdent($identification);
            $frontEditor->getFormContainer()->setType($data['type']);
            $frontEditor->addVariableTemplate('type', $data['type']);
            return $data;
        }
        return null;
    };

     $frontEditor->onLogout[] = function () {
        $this->handleFrontEditorDisable();
    };

    $frontEditor->onSuccess[] = function (Form $form, array $values) {
        try {
            if ($this['config']->editData((int) $values['id'], ['content' => $values['content']])) {
                $this->flashMessage($this->translator->translate('front-editor-onsuccess'), 'success');
            }
        } catch (\Dibi\Exception $e) {
            $this->flashMessage($e->getMessage(), 'danger');
        }
        $this->redirect('this');
    };
    return $frontEditor;
}
```

usage front:
```latte
<a n:href="FrontEditorDisable!" n:if="$frontEditorEnable" class="ajax">logout edit mode</a>

{control frontEditor:link 'identText1'} {control config:editor 'identText1'}

{control frontEditor}
```

front editor admin:
-------------------

presenters admin:
```php
use FrontEditorControl;

$this->template->frontEditorEnableLink = $this->getFrontEditorEnableLink();
```

usage admin:
```latte
<a href="{$baseUrl}/../{$frontEditorEnableLink}" title="{_'layout-front-editor-enable'}">link</a>
```
