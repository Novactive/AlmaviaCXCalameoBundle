CREATE TABLE calameo_publication
(
    contentobject_attribute_id int(11) NOT NULL DEFAULT 0,
    publication_id             text(255) NOT NULL DEFAULT 0,
    folder_id                  int(11) NOT NULL DEFAULT 0,
    version                    int(11) NOT NULL DEFAULT 0,
    PRIMARY KEY (contentobject_attribute_id, version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
