# MallFinder WordPress Theme

A comprehensive WordPress theme for creating a shopping mall directory website with interactive maps, store directories, and social media integration.

## Features

- **Custom Post Types**: Malls, Stores, Advertisements
- **Custom Taxonomies**: States, Cities, Areas, Store Categories, Amenities, Features
- **Interactive Maps**: Leaflet.js integration with custom markers
- **Search & Filter**: Advanced filtering by location, status, and keywords
- **Responsive Design**: Mobile-friendly with Tailwind-inspired CSS
- **Social Media Integration**: Share buttons and social links in header/footer
- **Admin Panel**: Easy-to-use settings page for site configuration

## Installation

1. **Download/Upload**
   - Download this theme as a ZIP file
   - Go to WordPress Admin → Appearance → Themes → Add New → Upload Theme
   - Or upload the `mallfinder-wordpress-theme` folder to `/wp-content/themes/`

2. **Activate the Theme**
   - Go to Appearance → Themes
   - Activate "MallFinder"

3. **Configure Settings**
   - Go to MallFinder → Site Settings
   - Enter your site name, tagline, and upload a logo
   - Configure map default center coordinates

4. **Set Up Social Media**
   - Go to MallFinder → Social Media
   - Add your social media URLs

## Setting Up Content

### States, Cities, Areas

1. Go to Malls → States and add your states/provinces
2. Go to Malls → Cities and add cities (set parent state)
3. Go to Malls → Areas and add areas (set parent city)

### Adding Malls

1. Go to Malls → Add New
2. Enter the mall name and description
3. Set the featured image
4. Fill in the mall details:
   - Status (Existing/Upcoming)
   - Address
   - Phone
   - Website
   - Opening Hours
   - Location coordinates (for map)
5. Assign State, City, and Area taxonomies
6. Assign Amenities and Features
7. Publish

### Adding Stores

1. Go to Stores → Add New
2. Enter the store name and description
3. Select the parent mall
4. Fill in store details:
   - Status (Open/Coming Soon/Closed)
   - Floor
   - Unit Number
   - Phone
   - Website
5. Assign a Store Category
6. Publish

### Managing Advertisements

1. Go to Advertisements → Add New
2. Enter the title
3. Set the position (Header, Footer, Sidebar, etc.)
4. Choose type (Image or HTML)
5. Upload an image or enter HTML code
6. Set link URL (for image ads)
7. Check "Active" to enable
8. Publish

## Customization

### Map Settings

In MallFinder → Site Settings, configure:
- Default Latitude
- Default Longitude  
- Default Zoom Level

### Styling

The theme uses CSS custom properties (variables) defined in `style.css`. Key variables:

```css
--color-emerald-500: #10b981;  /* Primary color */
--color-teal-500: #14b8a6;     /* Secondary color */
--color-amber-500: #f59e0b;    /* Upcoming mall color */
```

## Page Templates

- `front-page.php` - Home page with map and mall listings
- `single-mall.php` - Individual mall page with store directory
- `archive-mall.php` - Mall listing page
- `taxonomy-state.php` - Malls by state
- `taxonomy-city.php` - Malls by city
- `taxonomy-area.php` - Malls by area

## Shortcodes

### Social Share
```
[mallfinder_social_share url="https://example.com" title="Page Title"]
```

### Mall Card
```
[mallfinder_mall_card id="123"]
```

### Store Directory
```
[mallfinder_store_directory mall_id="123"]
```

## AJAX Endpoints

The theme uses WordPress AJAX API for:
- `get_malls` - Get malls for map markers
- `search_malls` - Search and filter malls
- `get_cities` - Get cities for a state
- `get_areas` - Get areas for a city

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher

## License

GNU General Public License v2 or later

## Credits

- Icons: Lucide Icons (SVG)
- Maps: Leaflet.js
- Fonts: System fonts

## Support

For support, please open an issue on GitHub or contact the theme developer.
