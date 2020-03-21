[![Latest stable version]][packagist] [![Total downloads]][packagist] [![License]][packagist]

# BetterEmbed integration for Neos CMS

BetterEmbed is a free service to easily integrate content like YouTube videos, Twitter and Facebook posts, blog posts, or any other kind of remote content.

## Benefits of BetterEmbed

-   easily integrate external content
-   improve privacy by keeping out unwanted tracking cookies (GDPR)
-   increase page speed by not loading external Javascript and CSS resources

If you want to know more, please visit https://www.acolono.com/betterembed to see the full feature list and get more information about the project.

## Installation

Add the dependency to your site package like this:

```bash
composer require --no-update betterembed/neos-embed
```

And then run `composer update` in your project's root folder.

## Dependencies

This package currently only requires Neos >= `4.3` and `guzzlehttp/guzzle`

## Usage

![BetterEmbed content element]

![BetterEmbed Placeholder]

### Examples

#### Website Example

![BetterEmbed example blog post]

```json
{
    "url": "https://www.neoscon.io/",
    "itemType": "Neos Conference | Tech- and Developer Conference",
    "title": "Neos Conference 2019 in Dresden",
    "body": "Neos Conference is designed for the community and to take everyone further. Because our audience deserves the best content. Everytime.",
    "thumbnailUrl": "https://www.neoscon.io/_Resources/Persistent/c799ecede3620e09013ac72d9f429942764c6a4e/neos_conference_2019-dresden-primary-web.jpg",
    "authorName": "@neoscms",
    "authorUrl": "https://twitter.com/neoscms"
}
```

#### YouTube Example

![BetterEmbed example Youtube]

```json
{
    "embedHtml": "<iframe width=\"480\" height=\"270\" src=\"https://www.youtube.com/embed/4DVD03IlXIs?feature=oembed\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>",
    "url": "https://www.youtube.com/watch?v=4DVD03IlXIs",
    "itemType": "YouTube",
    "title": "Why editors love Neos CMS | Open Source CMS",
    "body": "Neos is not your everyday Content Management System. Neos is based upon the idea to make editors and developers happy and to bring joy to their daily work. T...",
    "thumbnailUrl": "https://i.ytimg.com/vi/4DVD03IlXIs/hqdefault.jpg",
    "authorName": "Neos CMS",
    "authorUrl": "https://www.youtube.com/channel/UCQndryIh2s9i3htDrpb6tiw",
    "publishedAt": "2019-03-27T00:00:00+00:00"
}
```

## Rendering

The package includes a default rendering component together with an according to Javascript and CSS.

### Use your own renderer

You can easily register your own rendering component via extending `BetterEmbed.NeosEmbed:Component.Renderer`:

```elm
prototype(BetterEmbed.NeosEmbed:Component.Renderer) < prototype(Neos.Fusion:Case) {
    yourComponent {
        condition = true
        renderer = Your.Component:BetterEmbed {
            @apply.props = ${props}
        }
    }
}
```

## Asset handling

You will find the imported assets in the "BetterEmbed" asset collection within the Neos CMS Media Module. Each asset will be tagged with the itemType of the BetterEmbed response record for further grouping.

![BetterEmbed media module]

## API

The package is based on the BetterEmbed API endpoint.
You can read the API description [here][swagger] and test URLs.

## Beta Release

Please note that this is currently a beta release. If you find any issues pls report them or create PR - thx!

## Issues and PRs

Feel free to create issues for PRs if you like.

[packagist]: https://packagist.org/packages/betterembed/neos-betterembed
[latest stable version]: https://poser.pugx.org/betterembed/neos-betterembed/v/stable
[total downloads]: https://poser.pugx.org/betterembed/neos-betterembed/downloads
[license]: https://poser.pugx.org/betterembed/neos-betterembed/license
[betterembed content element]: Documentation/BetterEmbed-Content-Element.png
[betterembed placeholder]: Documentation/BetterEmbed-Placeholder.png
[betterembed example blog post]: Documentation/BetterEmbed-Example-BlogPost.png
[betterembed example youtube]: Documentation/BetterEmbed-Example-Youtube.png
[betterembed media module]: Documentation/BetterEmbed-Media-Module.png
[swagger]: https://api.betterembed.com/swagger/index.html
