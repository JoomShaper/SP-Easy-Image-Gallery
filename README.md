# SP Easy Image Gallery

[![Joomla!](https://img.shields.io/badge/Joomla!-4.x%20%7C%205.x-blue.svg?logo=joomla&logoColor=white)](https://www.joomla.org/)
[![License: GPL-2.0-or-later](https://img.shields.io/badge/License-GPL--2.0--or--later-green.svg)](LICENSE.txt)
[![Latest Release](https://img.shields.io/badge/Release-v2.1.2-brightgreen.svg)](https://github.com/JoomShaper/SP-Easy-Image-Gallery/releases)
[![Build Tool](https://img.shields.io/badge/Built%20with-Gulp-red.svg?logo=gulp&logoColor=white)](https://gulpjs.com/)

**SP Easy Image Gallery** is a fast, lightweight, and modern image gallery extension for Joomla! developed by [JoomShaper](https://www.joomshaper.com). It offers an intuitive backend interface for organizing albums and uploading pictures, paired with multiple frontend layout styles and a module to display galleries anywhere across your website.

---

## Table of Contents

- [Features](#features)
- [System Requirements](#system-requirements)
- [Repository Structure](#repository-structure)
- [Installation](#installation)
- [Building from Source](#building-from-source)
- [Usage & Configuration](#usage--configuration)
  - [1. Managing Categories](#1-managing-categories)
  - [2. Creating Albums & Uploading Photos](#2-creating-albums--uploading-photos)
  - [3. Displaying via Menu Items](#3-displaying-via-menu-items)
  - [4. Using the Gallery Module](#4-using-the-gallery-module)
- [Layout Styles](#layout-styles)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Categorized Albums**: Group albums into nested categories for effortless organization and filtering.
- **Drag-and-Drop Image Sorting**: Easily reorder images inside albums using an intuitive drag-and-drop interface.
- **Multiple Layout Presets**: Choose between **Default Grid**, **Mosaic**, and **Rectangle** presentations.
- **Granular Responsive Controls**: Configure columns and gutter spacing independently for Desktop, Tablet, and Mobile screens.
- **Interactive Lightbox Modal**: Built-in responsive popup preview featuring image titles, descriptions, and counter indicators.
- **Featured Albums**: Highlight specific albums and filter to showcase only featured items.
- **Dedicated Joomla Module (`mod_speasyimagegallery`)**: Display single albums or full album listings within any module position.
- **Security & Performance**: Built with CSRF protection, strict input sanitization, and optimized asset loading.

---

## System Requirements

| Requirement                      | Recommended / Supported    |
| :------------------------------- | :------------------------- |
| **Joomla!**                      | 4.x / 5.x / 6.x            |
| **PHP**                          | 7.4, 8.0, 8.1, 8.2, 8.3+   |
| **Database**                     | MySQL 5.7+ / MariaDB 10.4+ |
| **Node.js** _(development only)_ | 16.x or newer              |

---

## Repository Structure

The repository contains the source code for both the Joomla component and its companion site module:

```
speasyimagegallery/
├── administrator/
│   ├── components/com_speasyimagegallery/   # Admin backend controllers, models, views, and assets
│   └── language/en-GB/                      # Admin language files
├── components/
│   └── com_speasyimagegallery/              # Frontend component views, layouts, and public assets
├── language/en-GB/                          # Frontend component and module language files
├── modules/
│   └── mod_speasyimagegallery/              # Site module for displaying albums/galleries
├── gulpfile.js                              # Gulp build script for generating the installable zip
└── package.json                             # Node.js dependencies and project scripts
```

---

## Installation

### Using the Release Package

1. Download the latest `com_speasyimagegallery_fullpackage_v*.zip` from the [Releases](https://github.com/JoomShaper/SP-Easy-Image-Gallery/releases) page.
2. Log into your Joomla Administrator panel.
3. Navigate to **System > Install > Extensions**.
4. Drag and drop the downloaded ZIP file into the **Upload Package File** area.
5. The installer will automatically install both the **SP Easy Image Gallery** component and the **SP Easy Image Gallery Module**.

---

## Building from Source

To build the distributable zip package from the repository source code:

1. **Clone the repository**:

   ```bash
   git clone https://github.com/JoomShaper/SP-Easy-Image-Gallery.git
   cd SP-Easy-Image-Gallery
   ```

2. **Install Node dependencies**:

   ```bash
   npm install
   # or with yarn:
   yarn install
   ```

3. **Build the package**:

   ```bash
   npx gulp
   ```

4. **Output**:
   The compiled component files and the packaged zip will be generated inside the `dist/` directory:
   - `dist/component/` (compiled file tree)
   - `dist/component/com_speasyimagegallery_fullpackage_v2.1.1.zip` (installable Joomla package)

---

## Usage & Configuration

### 1. Managing Categories

1. In the Joomla Administrator panel, navigate to **Components > SP Easy Image Gallery > Categories**.
2. Click **New** to create a category (e.g., _Portfolios_, _Events_, _Nature_).

### 2. Creating Albums & Uploading Photos

1. Navigate to **Components > SP Easy Image Gallery > Albums**.
2. Click **New** to create a new album.
3. Enter the album title, assign a category, set the feature status, and upload a thumbnail cover image.
4. Upload images using the media uploader, and set individual titles, descriptions, and alt tags.
5. Drag and drop images to change their display order.
6. Click **Save & Close**.

### 3. Displaying via Menu Items

1. Go to **Menus > [Your Menu] > Add New Menu Item**.
2. In **Menu Item Type**, select **SP Easy Image Gallery**:
   - **Albums**: Displays a list of albums (optionally filtered by category or featured status).
   - **Album**: Displays all images from a single selected album.
3. Configure layout options (Default, Mosaic, or Rectangle), column counts, gutter widths, and lightbox preferences.
4. Save and publish the menu item.

### 4. Using the Gallery Module

1. Go to **Content > Site Modules** (or **System > Modules**).
2. Click **New** and choose **SP Easy Image Gallery**.
3. Choose the **Layout Type**:
   - **Album**: Shows images from a specific album.
   - **Albums**: Shows a grid of multiple albums.
4. Configure responsive columns, limits, and assign the module to your desired template position and pages.

---

## Layout Styles

- **Default**: A standard responsive grid layout where images maintain clean alignment.
- **Mosaic**: An asymmetrical dynamic grid suited for artistic photo compositions and mixed aspect ratios.
- **Rectangle**: A structured uniform layout optimizing image crops for uniform card displays.

---

## Contributing

Contributions, bug reports, and pull requests are always welcome!

1. Fork the repository.
2. Create your feature branch (`git checkout -b feature/my-feature`).
3. Commit your changes (`git commit -m 'Add new feature'`).
4. Push to the branch (`git push origin feature/my-feature`).
5. Open a Pull Request on GitHub.

For security concerns or bugs, please open an issue in the [Issue Tracker](https://github.com/JoomShaper/SP-Easy-Image-Gallery/issues).

---

## License

This project is open-source software licensed under the **GNU General Public License version 2 or later (GPL-2.0-or-later)**. See the [LICENSE.txt](LICENSE.txt) file for details.

Copyright &copy; 2010&ndash;2025 [JoomShaper](https://www.joomshaper.com). All rights reserved.
