# TryFont — WordPress Font Sampler Plugin

**TryFont** is a lightweight WordPress plugin that allows you to preview custom fonts on your site using a simple shortcode. Upload fonts via the admin dashboard and embed a live font sampler anywhere using `[tryfont font="yourfont.ttf"]`.

---

## 🎯 Features

- Upload and manage `.ttf`, `.woff`, and `.woff2` fonts
- Add a font sampler to any page/post via shortcode
- Customizable font preview controls (size, line-height, letter spacing)
- Set default controls in plugin settings
- Live preview with adjustable controls

---

## 🚀 Installation

1. Download the plugin `.zip` or clone this repo into your `wp-content/plugins` folder:
   ```bash
   git clone https://github.com/peter-moers/wordpress-tryfont.git wp-content/plugins/tryfont
   ```

2. Activate **TryFont** via the WordPress Admin > Plugins screen.

---

## ⚙️ Configuration

### 1. Open Plugin Settings

Go to:  
**WordPress Admin > Settings > TryFont**

There, you can:

- Upload new font files (`.ttf`, `.woff`, `.woff2`)
- Delete fonts you no longer need
- Set default values for:
  - Font size
  - Whether to show font size / line height / letter spacing sliders by default

---

## 📝 Shortcode Usage

To display a font sampler on a page or post:

```wordpress
[tryfont font="YourFontFile.ttf"]
```

### Optional Parameters:

```wordpress
[tryfont 
  font="YourFont.ttf"
  size="36"
  show_size="yes"
  show_lineheight="no"
  show_spacing="yes"
]
```

| Parameter       | Description                                | Values     |
|----------------|--------------------------------------------|------------|
| `font`         | Required. The filename of the uploaded font| e.g. `OpenSans.ttf` |
| `size`         | Initial font size in px                    | Number     |
| `show_size`    | Show font size control                     | `yes` / `no` |
| `show_lineheight` | Show line-height control               | `yes` / `no` |
| `show_spacing` | Show letter spacing control                | `yes` / `no` |

> Fonts can be uploaded via the settings page and will be stored in `/wp-content/uploads/fonts/`, for bulk uploading use ssh, ftp,...

---

## 📁 File Structure

```
tryfont/
├── tryfont.php         # Main plugin file
├── admin.php           # Admin settings page
├── js/frontend.js      # Font sampler JavaScript
├── css/style.css       # Optional CSS styling
└── readme.md           # You're reading this!
```

---


## 🧪 Contributing

Pull requests are welcome. Open an issue to discuss improvements, bugs, or ideas.
