# Use the latest version of PHP with Apache
FROM php:apache

# Enable Apache mod_rewrite (optional but commonly used)
RUN a2enmod rewrite

# WORKDIR
WORKDIR /var/www/html

COPY zendo ./zendo
COPY coins ./coins

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
