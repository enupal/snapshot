<p align="center">
	<a href="https://enupal.com/en/craft-plugins/enupal-snapshot/docs/" target="_blank">
	<img width="312" height="312" src="https://enupal.com/assets/docs/snapshot-icon.svg" alt="Enupal Snapshot"></a>
</p>

# Enupal Snapshot Plugin for Craft CMS 3.x

Snapshot from a url or a html page to generate a PDF or Image easily. It uses the excellent webkit-based wkhtmltopdf and wkhtmltoimage available on OSX, linux, windows.

## Features

### Display the Pdf in the browser from Html
```twig
{%  set settings = {
        filename: 'my-first.pdf'
    }
%}

{{ craft.enupalsnapshot.displayHtml("<h1>Hola mundo</h1>", settings) }}
```

### Display the Pdf in the browser from template

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

{{ craft.enupalsnapshot.displayHtml("<h1>Hola mundo</h1>", settings) }}
```

### Download url as an Image

```twig
{%  set settings = {
        filename: 'my-first-image.png',
        asImage: true,
    }
%}

{{ craft.enupalsnapshot.displayHtml("<h1>Hola mundo</h1>", settings) }}
```

### Display the Pdf in the browser from Urls

```twig
{% set urls = {0: 'https://www.google.com', 1:'http://enupal.com/en'} %}

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
            'cover': '<h1>Hello world from enupal snapshot</h1>',
            'header-font-size': '36',
            'header-html': 'pdfexamples/header.html',
            'footer-html': 'pdfexamples/header.html',
            'footer-right': null
        }
    }
%}

{{ craft.enupalsnapshot.displayHtml("<h1>Hola mundo</h1>", settings) }}
```

## Documentation

https://enupal.com/en/craft-plugins/enupal-snapshot/docs/

## Enupal Snapshot Support

Via Email:

Send us a note at: info@enupal.com

------------------------------------------------------------

Brought to you by [enupal](https://enupal.com/en)

<p align="center">
	<a href="https://enupal.com/en" target="_blank">
	<img width="169" height="35" src="https://enupal.com/assets/docs/enupal-logo.png" alt="Enupal Snapshot"></a>
</p>