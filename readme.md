[![Latest stable version]][packagist] [![Total downloads]][packagist] [![License]][packagist]

# BetterEmbed integration for Neos CMS

BetterEmbed is a free service to easily integrate content like Twitter posts, blog posts, or any other kind of remote content.

**Notice**: Due GDPR consent access gateways, not every URL will work. For instance there are known issues with Facebook, and Instagram.

## Benefits of BetterEmbed

-   easily integrate external content
-   improve privacy by keeping out unwanted tracking cookies (GDPR)
-   increase page speed by not loading external Javascript and CSS resources

If you want to know more, please visit https://betterembed.com to see the full feature list and get more information about the project.

## Installation

Add the dependency to your site package like this:

```bash
composer require --no-update betterembed/neos-betterembed
```

And then run `composer update` in your project's root folder.

## Dependencies

This package currently only requires Neos >= `8` and `guzzlehttp/guzzle`

## Usage

![BetterEmbed content element]

![BetterEmbed Placeholder]

### Examples

#### Website Example

![BetterEmbed example blog post]

##### API result

```json
{
    "url": "https://www.theverge.com/2020/3/23/21175407/remote-work-working-from-home-guide-how-to-tips-video-conference-calls-laptops-zoom-slack",
    "itemType": "The Verge",
    "title": "The Verge Guide to Working at Home",
    "body": "Tips and tricks on being a remote worker",
    "thumbnailUrl": "https://cdn.vox-cdn.com/thumbor/NdRt13PSWplEE-lZkxFEPyc4a8U=/0x146:2040x1214/fit-in/1200x630/cdn.vox-cdn.com/uploads/chorus_asset/file/19822359/acastro_200319_3941_wfhGuide_0003.jpg",
    "authorName": "Barbara Krasnoff",
    "publishedAt": "2020-03-23T08:00:00-04:00"
}
```

#### YouTube Example

![BetterEmbed example Youtube 1]
![BetterEmbed example Youtube 2]
![BetterEmbed example Youtube 3]

##### API result

```json
{
    "embedHtml": "<iframe width=\"480\" height=\"270\" src=\"https://www.youtube.com/embed/-ZHY85m63k0?feature=oembed\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>",
    "url": "https://www.youtube.com/watch?v=-ZHY85m63k0",
    "itemType": "YouTube",
    "title": "Neos Con 2019 Teaser Trailer | Neos Conference",
    "body": "Only a few weeks and tickets left till the Neos Con 2019 in Dresden from 10th to 11th May. Be sure to save your spot: https://www.eventbrite.de/e/neos-confer...",
    "thumbnailUrl": "https://i.ytimg.com/vi/-ZHY85m63k0/hqdefault.jpg",
    "authorName": "Neos CMS",
    "authorUrl": "https://www.youtube.com/channel/UCQndryIh2s9i3htDrpb6tiw",
    "publishedAt": "2019-03-29T00:00:00+00:00"
}
```

#### Twitter Example

![BetterEmbed example Twitter 1]
![BetterEmbed example Twitter 2]
![BetterEmbed example Twitter 3]

```json
{
    "embedHtml": "<blockquote class=\"twitter-tweet\"><p lang=\"en\" dir=\"ltr\">What a blast! ☺️ Two days full of exciting talks, conversations, networking and way more! Thank you all for being part of it! We hope to see you all again soon! 🥰 <a href=\"https://twitter.com/hashtag/neoscon?src=hash&amp;ref_src=twsrc%5Etfw\">#neoscon</a> <a href=\"https://twitter.com/hashtag/neos?src=hash&amp;ref_src=twsrc%5Etfw\">#neos</a> <a href=\"https://twitter.com/hashtag/community?src=hash&amp;ref_src=twsrc%5Etfw\">#community</a> <a href=\"https://t.co/nSaZCU2bba\">pic.twitter.com/nSaZCU2bba</a></p>&mdash; The Neos Project (@neoscms) <a href=\"https://twitter.com/neoscms/status/1127605365019947008?ref_src=twsrc%5Etfw\">May 12, 2019</a></blockquote>\n<script async src=\"https://platform.twitter.com/widgets.js\" charset=\"utf-8\"></script>",
    "url": "https://twitter.com/neoscms/status/1127605365019947008",
    "itemType": "Twitter",
    "title": "The Neos Project on Twitter",
    "body": "“What a blast! ☺️ Two days full of exciting talks, conversations, networking and way more! Thank you all for being part of it! We hope to see you all again soon! 🥰 #neoscon #neos #community”",
    "thumbnailUrl": "https://pbs.twimg.com/media/D6YN2puXkAE_QLr.jpg:large",
    "authorName": "The Neos Project",
    "authorUrl": "https://twitter.com/neoscms",
    "publishedAt": "2019-05-12T16:04:03+00:00"
}
```

## Rendering

The package includes a default rendering component together with the according Javascript and CSS sources.

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

You will find the imported assets in the "BetterEmbed" asset collection within the Neos CMS Media Module. Each asset will be tagged with the `itemType` of the BetterEmbed response record for further grouping.

![BetterEmbed media module]

## API

The package is based on the BetterEmbed API endpoint.
You can read the API description [here][swagger] and test URLs.

## Issues and PRs

Feel free to create issues for PRs if you like.

[packagist]: https://packagist.org/packages/betterembed/neos-betterembed
[latest stable version]: https://poser.pugx.org/betterembed/neos-betterembed/v/stable
[total downloads]: https://poser.pugx.org/betterembed/neos-betterembed/downloads
[license]: https://poser.pugx.org/betterembed/neos-betterembed/license
[betterembed content element]: Documentation/BetterEmbed-Content-Element.png
[betterembed placeholder]: Documentation/BetterEmbed-Placeholder.png
[betterembed example blog post]: Documentation/BetterEmbed-Example-BlogPost.png
[betterembed example youtube 1]: Documentation/BetterEmbed-Example-Youtube-1.png
[betterembed example youtube 2]: Documentation/BetterEmbed-Example-Youtube-2.png
[betterembed example youtube 3]: Documentation/BetterEmbed-Example-Youtube-3.png
[betterembed example twitter 1]: Documentation/BetterEmbed-Example-Twitter-1.png
[betterembed example twitter 2]: Documentation/BetterEmbed-Example-Twitter-2.png
[betterembed example twitter 3]: Documentation/BetterEmbed-Example-Twitter-3.png
[betterembed media module]: Documentation/BetterEmbed-Media-Module.png
[swagger]: https://api.betterembed.com/swagger/index.html
