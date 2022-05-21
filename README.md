<p align="center">
<img src="https://scrutinizer-ci.com/g/enupal/snapshot/badges/quality-score.png?b=master"> <img src="https://scrutinizer-ci.com/g/enupal/snapshot/badges/coverage.png?b=master"> <img src="https://scrutinizer-ci.com/g/enupal/snapshot/badges/build.png?b=master"> <img src="https://scrutinizer-ci.com/g/enupal/snapshot/badges/code-intelligence.svg?b=master">
</p>
<p align="center">
	<a href="https://docs.enupal.com/enupal-snapshot/" target="_blank">
	<img width="212" height="212" src="https://enupal.com/assets/docs/snapshot-icon.svg" alt="Enupal Snapshot"></a>
</p>

# Enupal Snapshot Plugin for Craft CMS

PDF or Image generation from a URL or HTML page easily. It uses the excellent webkit-based wkhtmltopdf and wkhtmltoimage available on OSX, Linux & windows.
## Features

### Store your PDF or Image files in Assets

Enupal Snapshot allows set a global asset and sub-path (twig code allowed) to store your files. Override the asset and sub-path before generating your files in your templates, more info [here](https://enupal.com/craft-plugins/enupal-snapshot/docs/advanced/override-upload-asset).

### Display the Pdf in browser from Html
```twig
{%  set settings = {
        filename: 'my-first.pdf'
    }
%}

{{ craft.enupalsnapshot.displayHtml("<h1>Hello world!</h1>", settings) }}
```

### Display the Pdf in browser from template

```twig
{%  set settings = {
        filename: 'my-first.pdf',
        variables: {
            foo: 'barr'
        }
    }
%}

{{ craft.enupalsnapshot.displayTemplate("pdf/examples/summary", settings) }}
```

### Download url of the Pdf from Html

```twig
{%  set settings = {
        filename: 'my-first.pdf',
        inline: false,
    }
%}

{% set url = craft.enupalsnapshot.displayHtml("<h1>Hello world!</h1>", settings) %}

<a target="_blank" href="{{url}}"> Download Pdf</a>
```

### Download url as an Image

```twig
{%  set settings = {
        filename: 'my-first-image.png',
        asImage: true
    }
%}

{% set url = craft.enupalsnapshot.displayHtml("<h1>Hello world!</h1>", settings) %}

<a target="_blank" href="{{url}}"> Download Image</a>

```

### Display the Pdf in browser from Urls

```twig
{% set urls = {0: 'https://www.google.com', 1:'http://enupal.com'} %}

{%  set settings = {
        filename: 'my-first.pdf'
    }
%}

{{ craft.enupalsnapshot.displayUrl(urls, settings) }}
```

### Add cliOptions

All available options [here](https://wkhtmltopdf.org/usage/wkhtmltopdf.txt): 

```twig
{%  set settings = {
        filename: 'my-first.pdf',
        cliOptions: {
            'cover': '<h1>Hello world from Enupal Snapshot</h1>',
            'header-font-size': '36',
            'footer-right': null,
            'orientation': 'Portrait',
            'page-size': 'A4'
        }
    }
%}

{{ craft.enupalsnapshot.displayHtml("<h1>Hello world!</h1>", settings) }}
```

## Documentation

https://docs.enupal.com/enupal-snapshot/

## Enupal Snapshot Support

* Send us a note at: support@enupal.com

* Create an [issue](https://github.com/enupal/snapshot/issues) on Github

------------------------------------------------------------

Brought to you by [enupal](https://enupal.com)

<p align="center">
	<a href="https://enupal.com/en" target="_blank">
	<img width="169" height="35" src="https://enupal.com/assets/docs/enupal-logo.png" alt="Enupal Snapshot"></a>
</p>