# [`render` helper](../../Classes/Renderer/Helper/RenderHelper.php)

## Reference

<https://fractal.build/guide/core-concepts/view-templates.html#render>

## Description

This helper implements the `render` helper known from Fractal. It is used to render
templates based on a specific context. Instead of a default context an alternative
context can be passed, additionally both contexts can also be merged.

To define a default context, the renderer must be passed an array whose key matches
the name of the template to be rendered.

## Examples

### Example 1: Default context only

```php
$renderData = [
    // Default context for the template modules/message/message.hbs (see below)
    '@message' => [
        'class' => 'message--alert',
        'title' => 'Attention',
        'message' => 'Hello world, you are in danger!',
    ],
];
$this->renderer->render('pages/default/default', $renderData);
```

Templates:

```handlebars
{{! pages/default/default.hbs}}

<div class="page">
    {{render '@message'}}
</div>
```

```handlebars
{{! modules/message/message.hbs}}

<div class="message {{ class }}">
    {{#if title}}
        <strong>{{ title }}</strong>
    {{/if}}
    {{#if message}}
        <p>{{ message }}</p>
    {{/if}}
</div>
```

This produces the following output:

```html
<div class="page">
    <div class="message message--alert">
        <strong>Attention</strong>
        <p>Hello world, you are in danger!</p>
    </div>
</div>
```

### Example 2: Usage of either default context or custom context

```php
$renderData = [
    // Default context for the template modules/menu/menu-items.hbs (see below)
    '@menu-items' => [
        'items' => [
            [
                'class' => 'menu-item--home',
                'title' => 'Home',
            ],
            [
                'class' => 'menu-item--services',
                'title' => 'Services',
                'subItems' => [
                    [
                        'title' => 'Service A',
                    ],
                    [
                        'title' => 'Service B',
                    ],
                ],
            ],
        ],
    ],
];
$this->renderer->render('modules/menu/menu', $renderData);
```

Templates:

```handlebars
{{! modules/menu/menu.hbs}}

<ul class="menu">
    {{render '@menu-items'}}
</ul>
```

```handlebars
{{! modules/menu/menu-items.hbs}}

{{#each items}}
    <li class="menu-item {{ class }}">
        <span>{{ title }}</span>
        {{#if subItems}}
            <ul class="submenu">
                {{#each subItems}}
                    {{render '@menu-items' subItems}}
                {{/each}}
            </ul>
        {{/if}}
    </li>
{{/each}}
```

This produces the following output:

```html
<ul class="menu">
    <li class="menu-item menu-item--home">
        <span>Home</span>
    </li>
    <li class="menu-item menu-item--services">
        <span>Services</span>
        <ul class="submenu">
            <li class="menu-item">
                Service A
            </li>
            <li class="menu-item">
                Service B
            </li>
        </ul>
    </li>
</ul>
```

### Example 3: Merge default context with custom context

Contexts can be merged by using the additional parameter `merge=true`.

```php
$renderData = [
    // Default context for the template modules/message/message.hbs (see below)
    '@message' => [
        'class' => 'message--default',
    ],
    // Custom render data
    'messages' => [
        [
            'title' => 'Welcome',
            'message' => 'Hello world, it\'s nice to see you!',
        ],
        [
            'class' => 'message--alert',
            'title' => 'Attention',
            'message' => 'Hello world, you are in danger!',
        ],
    ],
];
$this->renderer->render('pages/default/default', $renderData);
```

Templates:

```handlebars
{{! pages/default/default.hbs}}

<div class="page">
    {{#each messages}}
        {{render '@message' this merge=true}}
    {{/each}}
</div>
```

```handlebars
{{! modules/message/message.hbs}}

<div class="message {{ class }}">
    {{#if title}}
        <strong>{{ title }}</strong>
    {{/if}}
    {{#if message}}
        <p>{{ message }}</p>
    {{/if}}
</div>
```

This produces the following output:

```html
<div class="page">
    <div class="message message--default">
        <strong>Welcome</strong>
        <p>Hello world, it's nice to see you!</p>
    </div>
    <div class="message message--alert">
        <strong>Attention</strong>
        <p>Hello world, you are in danger!</p>
    </div>
</div>
```