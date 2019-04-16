-- Table: public.sales_order_investor

-- DROP TABLE public.sales_order_investor;

CREATE TABLE public.sales_order_investor
(
  id integer NOT NULL DEFAULT nextval('sales_order_investor_id_seq'::regclass),
  name character varying, -- Name
  amount double precision, -- Jumlah
  sales_order_id integer, -- Sales Order
  partner_id integer, -- Investor
  create_uid integer, -- Created by
  create_date timestamp without time zone, -- Created on
  write_uid integer, -- Last Updated by
  write_date timestamp without time zone, -- Last Updated on
  CONSTRAINT sales_order_investor_pkey PRIMARY KEY (id),
  CONSTRAINT sales_order_investor_create_uid_fkey FOREIGN KEY (create_uid)
      REFERENCES public.res_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT sales_order_investor_partner_id_fkey FOREIGN KEY (partner_id)
      REFERENCES public.res_partner (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT sales_order_investor_sales_order_id_fkey FOREIGN KEY (sales_order_id)
      REFERENCES public.sale_order (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL,
  CONSTRAINT sales_order_investor_write_uid_fkey FOREIGN KEY (write_uid)
      REFERENCES public.res_users (id) MATCH SIMPLE
      ON UPDATE NO ACTION ON DELETE SET NULL
)
WITH (
  OIDS=FALSE
);
ALTER TABLE public.sales_order_investor
  OWNER TO openpg;
COMMENT ON TABLE public.sales_order_investor
  IS 'mapping SO Investor';
COMMENT ON COLUMN public.sales_order_investor.name IS 'Name';
COMMENT ON COLUMN public.sales_order_investor.amount IS 'Jumlah';
COMMENT ON COLUMN public.sales_order_investor.sales_order_id IS 'Sales Order';
COMMENT ON COLUMN public.sales_order_investor.partner_id IS 'Investor';
COMMENT ON COLUMN public.sales_order_investor.create_uid IS 'Created by';
COMMENT ON COLUMN public.sales_order_investor.create_date IS 'Created on';
COMMENT ON COLUMN public.sales_order_investor.write_uid IS 'Last Updated by';
COMMENT ON COLUMN public.sales_order_investor.write_date IS 'Last Updated on';

