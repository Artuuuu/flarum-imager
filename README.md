<h1 align="center">Flarum Imager</h1>
<p align="center">
  <img src="https://img.shields.io/github/release/Artuuuu/flarum-imager.svg" />
  <img src="https://img.shields.io/github/release-date/Artuuuu/flarum-imager.svg" />
  <img src="https://img.shields.io/github/languages/top/Artuuuu/flarum-imager.svg" />
  <a href="https://packagist.org/packages/Artuu/flarum-imager">
    <img src="https://img.shields.io/packagist/dt/Artuu/flarum-imager.svg" target="_blank" />
  </a>
  <a href="https://github.com/Artuuuu/flarum-imager/blob/master/LICENSE">
    <img src="https://img.shields.io/badge/license-MIT-yellow.svg" target="_blank" />
  </a>
</p>

> Allows you to set default sizes for photos uploaded to the forum and change their size in the url.

## 💼 How does it work?

The extension creates its own folder (`public/assets/imager`), where it places images in a higher resolution. This extension allows you to set the size in the url, maximum up to the photo size. The default saving of photos (from flarum) also works.

## 📚 What you should know

- The images are in the above mentioned folder.
- The url syntax is as follows
  - Favicon `http://localhost/assets/favicon?s=64`
  - Logo `http://localhost/assets/logo?s=60`
  - User avatar `http://localhost/assets/avatars/{user_avatarUrl}?s=100`
- The variable `?s={size}` in the url indicates the size of the image that should display the extension.
- You can set up a group of users who may have a larger avatar size.

## 🚀 Installation

To download the extension you can use [Bazaar](https://discuss.flarum.org/d/5151-flagrow-bazaar-the-extension-marketplace) or install it through composer:

```bash
composer require artuu/flarum-imager
```

## ✨ Demo

Screenshots from:

- [Sizing user avatar by url](https://imgur.com/a/qfOIcSR)
- [Sizing logo by url](https://imgur.com/a/qbOmimG)
- [Sizing favicon by url](https://imgur.com/a/O3NCMxn)

Screenshot from `Imager Settings`:

<p align="center">
  <img width="700" align="center" src="https://i.imgur.com/7G32OHP.png" alt="Imager Settings"/>
</p>

## 🔗 Links

- [Flarum Discuss post](https://discuss.flarum.org/d/24202)
- [Source code on GitHub](https://github.com/Artuuuu/flarum-imager)
- [Flagrow Extension](https://flagrow.io/extensions/Artuu/flarum-imager)
- [Download via Packagist](https://packagist.org/packages/Artuu/flarum-imager)

## 📝 License

Copyright © 2020 [Artuu](https://github.com/Artuuuu).<br /> This project is [MIT](https://github.com/Artuuuu/flarum-imager/blob/master/LICENSE) licensed.

---

_Extension created by [@Artuu](https://github.com/Artuuuu), the owner of [My Kill](https://mykill.pl)_
