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
"geniv/nette-general-form": ">=1.0.0"
```

Include in application
----------------------

neon configure:
```neon
# front editor
frontEditor:
#   autowired: true
#   formContainer: FrontEditor\FormContainer
```

neon configure extension:
```neon
extensions:
    frontEditor: FrontEditor\Bridges\Nette\Extension
```

presenters:
```php
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
