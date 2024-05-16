CREATE TABLE IF NOT EXISTS todo (
    id uuid PRIMARY KEY,
    data json NOT NULL
);