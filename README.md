# Static Eduloom Education Website

This is a static version of the Eduloom Education website, designed to be deployed on shared hosting environments. It includes a contact form that saves submissions to a PostgreSQL database.

## Features

- Fully responsive design
- Contact form with client-side validation
- Server-side form processing with PHP
- Database integration for form submissions
- No JavaScript framework required

## Requirements

- Web server (Apache, Nginx, etc.)
- PHP 7.4 or higher
- PostgreSQL database
- Web browser with JavaScript enabled

## Installation

1. **Upload Files**
   - Upload all files to your web server's public directory (usually `public_html` or `htdocs`).

2. **Database Setup**
   - Create a new PostgreSQL database on your hosting provider.
   - The `submit-form.php` script will automatically create the required table on the first form submission.

3. **Configure Database Connection**
   - Open `submit-form.php` in a text editor.
   - Update the database connection details:
     ```php
     $host = 'localhost'; // Your database host
     $dbname = 'your_database_name';
     $username = 'your_database_username';
     $password = 'your_database_password';
     ```

4. **Set Permissions**
   - Ensure the web server has write permissions to the directory where the database file will be created (if using SQLite).
   - For security, set the permissions of `submit-form.php` to 644.

5. **Email Notifications (Optional)**
   - To receive email notifications for form submissions, uncomment and configure the `mail()` function in `submit-form.php`.

## File Structure

```
static-eduloom-education/
├── index.html          # Main HTML file
├── submit-form.php     # Form submission handler
├── css/
│   └── styles.css      # All styles
├── js/
│   └── main.js         # JavaScript functionality
└── images/             # Image assets
```

## Customization

- **Colors**: Edit the CSS variables in `css/styles.css` to match your brand colors.
- **Content**: Update the text and images in `index.html`.
- **Form Fields**: Modify the form in `index.html` and update the database schema in `submit-form.php` if you need different fields.

## Security Notes

- The form includes basic client-side validation but always validate input on the server side as well.
- Consider implementing CSRF protection for the form.
- Keep your database credentials secure and never commit them to version control.
- For production, consider using environment variables for sensitive information.

## Troubleshooting

- **Form not submitting**: Check the browser's developer console for JavaScript errors.
- **Database connection issues**: Verify your database credentials and ensure the PostgreSQL server is running.
- **Emails not sending**: Check your server's mail configuration and error logs.

## License

This project is open source and available under the [MIT License](LICENSE).
