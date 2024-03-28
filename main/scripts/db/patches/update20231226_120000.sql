CREATE TABLE IF NOT EXISTS user_auth_providers
(
    id            int unsigned auto_increment primary key,
    provider_id   varchar(255) default '' not null,
    provider_type varchar(255) default '' not null,
    user_id       int unsigned            not null,
    dt            int unsigned            not null,
    constraint unique_providerId_providerType unique (provider_id, provider_type)
);
