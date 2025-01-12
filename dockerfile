# Use the latest PHP with Apache
FROM php:apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Switch to www-data user
USER www-data

# Set work directory
WORKDIR /var/www/html

# Copy application files with ownership set
COPY --chown=www-data:www-data zendo ./zendo
COPY --chown=www-data:www-data coins ./coins

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
