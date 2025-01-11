# Use the latest PHP with Apache
FROM php:apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Set work directory
WORKDIR /var/www/html

# Copy application files
COPY zendo ./zendo
COPY coins ./coins

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
