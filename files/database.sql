CREATE TABLE user (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(128) NOT NULL,
    username VARCHAR(64) NOT NULL,
    password VARCHAR(64) NOT NULL,
    salt VARCHAR(32) NOT NULL,
    created DATETIME NOT NULL,
    modified DATETIME,
    recycled DATETIME,

    UNIQUE INDEX (username)
);

CREATE TABLE page (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    parent_id INT UNSIGNED,
    page_type VARCHAR(32) NOT NULL DEFAULT 'page',
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(128),
    settings MEDIUMTEXT,
    revision INT NOT NULL DEFAULT 1,
    is_finalized TINYINT(1),
    publish_date DATETIME,
    archive_date DATETIME,
    created DATETIME NOT NULL,
    modified DATETIME,
    recycled DATETIME
);

CREATE TABLE block (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    parent_id INT UNSIGNED,
    block_type VARCHAR(128) NOT NULL,
    headline VARCHAR(255),
    content MEDIUMTEXT,
    settings MEDIUMTEXT NOT NULL,
    revision INT NOT NULL DEFAULT 1,
    is_finalized TINYINT(1),
    created DATETIME NOT NULL,
    modified DATETIME,
    recycled DATETIME
);

CREATE TABLE page_has_block (
    page_id INT UNSIGNED NOT NULL,
    block_id INT UNSIGNED NOT NULL,
    lft INT,
    rgt INT,
    is_visible TINYINT(1),
    created DATETIME NOT NULL,
    modified DATETIME,

    PRIMARY KEY (page_id, block_id),
    FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE,
    FOREIGN KEY (block_id) REFERENCES block (id) ON DELETE CASCADE
);

CREATE TABLE page_has_block_recycle (
    page_id INT UNSIGNED NOT NULL,
    block_id INT UNSIGNED NOT NULL,
    parent_block_id INT UNSIGNED,
    next_block_id INT UNSIGNED,
    recycled DATETIME NOT NULL
);

# Single site

CREATE TABLE page_menu (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    page_id INT UNSIGNED NOT NULL,
    lft INT,
    rgt INT,
    url VARCHAR(128),
    is_visible TINYINT(1),
    is_innavigation TINYINT(1),
    created DATETIME NOT NULL,
    modified DATETIME,

    FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE
);

# Multisite

CREATE TABLE site (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    theme VARCHAR(255) NOT NULL,
    locale VARCHAR(10) NOT NULL DEFAULT 'en_US'
    created DATETIME NOT NULL,
    modified DATETIME,
    recycled DATETIME
);

CREATE TABLE site_has_user (
    site_id INT UNSIGNED NOT NULL,
    user_id INT UNSIGNED NOT NULL,
    user_role VARCHAR(32) NOT NULL DEFAULT 'Visitor'
);

CREATE TABLE site_has_page (
    site_id INT UNSIGNED NOT NULL,
    page_id INT UNSIGNED NOT NULL,
    lft INT,
    rgt INT,
    url VARCHAR(128),
    is_visible TINYINT(1),
    is_innavigation TINYINT(1),
    created DATETIME NOT NULL,
    modified DATETIME,

    PRIMARY KEY (site_id, page_id),
    FOREIGN KEY (site_id) REFERENCES site (id) ON DELETE CASCADE,
    FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE
);

CREATE TABLE site_has_page_recycle (
    site_id INT UNSIGNED NOT NULL,
    page_id INT UNSIGNED NOT NULL,
    parent_page_id INT UNSIGNED,
    next_page_id INT UNSIGNED,
    recycled DATETIME NOT NULL
);

CREATE TABLE settings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    site_id INT UNSIGNED,
    page_id INT UNSIGNED,
    block_id INT UNSIGNED,
    user_id INT UNSIGNED,
    `key` VARCHAR(64) NOT NULL,
    `str_value` VARCHAR(255),
    `int_value` INT,
    `float_value` DECIMAL(10,4),
    created DATETIME NOT NULL,
    modified DATETIME,
);

CREATE TABLE changelog (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    user_id INT UNSIGNED NOT NULL,
    table_name VARCHAR(64) NOT NULL,
    row_id INT UNSIGNED NOT NULL,
    action VARCHAR(64) NOT NULL,
    data MEDIUMTEXT,
    created DATETIME NOT NULL,

    INDEX (table_name, row_id),
    FOREIGN KEY (user_id) REFERENCES user (id)
);
