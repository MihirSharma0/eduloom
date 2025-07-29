# MySQL Database Setup for Eduloom Education

This document provides instructions for setting up and connecting the Eduloom Education website to a MySQL database.

## Quick Start with Docker

### 1. Start the MySQL Database
```bash
docker-compose up -d
```

This will start:
- MySQL 8.0 database on port 3306
- phpMyAdmin on port 8080 for database management

### 2. Database Credentials
- **Host**: localhost
- **Port**: 3306
- **Database**: eduloom_education_db
- **Username**: eduloom_education_user
- **Password**: eduloom_education_pass
- **Root Password**: eduloom_root_pass

### 3. Access phpMyAdmin
Open your browser and go to: http://localhost:8080
- **Server**: db
- **Username**: eduloom_education_user
- **Password**: eduloom_education_pass

## Database Schema

The database includes the following tables:

### 1. enquiries
Stores form submissions from the website contact form.
```sql
CREATE TABLE enquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(50) NOT NULL,
    program VARCHAR(100) NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 2. universities
Stores information about partner universities.
```sql
CREATE TABLE universities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(100) NOT NULL,
    ranking INT,
    website VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### 3. programs
Stores academic programs offered by universities.
```sql
CREATE TABLE programs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    university_id INT,
    name VARCHAR(255) NOT NULL,
    degree_type ENUM('undergraduate', 'graduate', 'language', 'certificate'),
    duration_months INT,
    tuition_fee DECIMAL(10,2),
    description TEXT,
    FOREIGN KEY (university_id) REFERENCES universities(id)
);
```

## Manual MySQL Setup (Alternative)

If you prefer to set up MySQL manually instead of using Docker:

### 1. Install MySQL
Download and install MySQL 8.0 from https://dev.mysql.com/downloads/

### 2. Create Database and User
```sql
CREATE DATABASE eduloom_education_db;
CREATE USER 'eduloom_education_user'@'localhost' IDENTIFIED BY 'eduloom_education_pass';
GRANT ALL PRIVILEGES ON eduloom_education_db.* TO 'eduloom_education_user'@'localhost';
FLUSH PRIVILEGES;
```

### 3. Run the Initialization Script
Execute the `init.sql` file to create tables and sample data:
```bash
mysql -u eduloom_education_user -p eduloom_education_db < init.sql
```

## PHP Configuration

The website's PHP form submission (`submit-form.php`) is configured to connect to MySQL with the following settings:

```php
$host = 'localhost';
$dbname = 'eduloom_education_db';
$username = 'eduloom_education_user';
$password = 'eduloom_education_pass';
$port = '3306';
```

## Testing the Connection

1. Start the database: `docker-compose up -d`
2. Open the website in your browser
3. Fill out the contact form and submit
4. Check phpMyAdmin to see if the enquiry was saved

## Troubleshooting

### Connection Issues
- Ensure MySQL is running: `docker-compose ps`
- Check database logs: `docker-compose logs db`
- Verify credentials in `submit-form.php`

### Permission Issues
- Ensure the database user has proper permissions
- Check that the database exists and is accessible

### PHP Issues
- Ensure PHP has the PDO MySQL extension installed
- Check PHP error logs for connection errors

## Production Deployment

For production deployment:

1. **Security**: Change default passwords
2. **SSL**: Use SSL connections to the database
3. **Environment Variables**: Store credentials in environment variables
4. **Backup**: Set up regular database backups
5. **Monitoring**: Implement database monitoring

## Sample Data

The initialization script includes sample data for:
- 2 sample enquiries
- 8 top universities with rankings
- Basic user and program structures for future expansion

You can modify or remove this sample data as needed for your production environment.
