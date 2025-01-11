# Use the latest PHP with Apache
FROM php:apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Switch to www-data user
USER www-data

# Set work directory
WORKDIR /var/www/html

# Copy application files
COPY zendo ./zendo
COPY coins ./coins

# Set ownership of the application directory to www-data
RUN chown -R www-data:www-data /var/www/html

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
