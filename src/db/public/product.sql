CREATE TABLE product (
  id         SERIAL PRIMARY KEY,
  name       VARCHAR(512)   NOT NULL,
  tags       VARCHAR(1024)  NOT NULL,
  price      NUMERIC(12, 2) NOT NULL,
  created_at TIMESTAMPTZ    NOT NULL,
  updated_at TIMESTAMPTZ    NOT NULL
);
