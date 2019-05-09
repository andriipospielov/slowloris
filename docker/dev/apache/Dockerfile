FROM httpd:2.4

RUN apt-get update \
    && apt-get -qqy install \
        apache2-doc \
        apache2-utils \
        libapache2-mod-security2 \
        libapache2-mod-qos \

    && apt-get clean; rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN cp /usr/lib/apache2/modules/mod_security2.so /usr/local/apache2/modules/mod_security2.so \
    && cp /usr/lib/apache2/modules/mod_qos.so       /usr/local/apache2/modules/mod_qos.so

RUN a2enmod \
        security2 \
        qos
