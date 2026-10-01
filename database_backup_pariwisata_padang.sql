--
-- PostgreSQL database dump
--

\restrict FxXsfOKgJp27SrndtEKfLnyBWcCHztwJ4tARhKepfcOUMQUUm8sciHRtScmdWyh

-- Dumped from database version 16.14 (Ubuntu 16.14-0ubuntu0.24.04.1)
-- Dumped by pg_dump version 16.14 (Ubuntu 16.14-0ubuntu0.24.04.1)

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- Name: cache; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO jis;

--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO jis;

--
-- Name: chat_messages; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.chat_messages (
    id bigint NOT NULL,
    session_id bigint NOT NULL,
    role character varying(255) NOT NULL,
    pesan text NOT NULL,
    intent_json json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT chat_messages_role_check CHECK (((role)::text = ANY ((ARRAY['user'::character varying, 'assistant'::character varying])::text[])))
);


ALTER TABLE public.chat_messages OWNER TO jis;

--
-- Name: chat_messages_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.chat_messages_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.chat_messages_id_seq OWNER TO jis;

--
-- Name: chat_messages_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.chat_messages_id_seq OWNED BY public.chat_messages.id;


--
-- Name: chat_sessions; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.chat_sessions (
    id bigint NOT NULL,
    session_token character varying(64) NOT NULL,
    lat numeric(10,7),
    lng numeric(10,7),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.chat_sessions OWNER TO jis;

--
-- Name: chat_sessions_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.chat_sessions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.chat_sessions_id_seq OWNER TO jis;

--
-- Name: chat_sessions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.chat_sessions_id_seq OWNED BY public.chat_sessions.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO jis;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO jis;

--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO jis;

--
-- Name: jobs; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO jis;

--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO jis;

--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: kategori; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.kategori (
    id bigint NOT NULL,
    nama character varying(50) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.kategori OWNER TO jis;

--
-- Name: kategori_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.kategori_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.kategori_id_seq OWNER TO jis;

--
-- Name: kategori_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.kategori_id_seq OWNED BY public.kategori.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO jis;

--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO jis;

--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO jis;

--
-- Name: sessions; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO jis;

--
-- Name: users; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    role character varying(20) DEFAULT 'user'::character varying NOT NULL
);


ALTER TABLE public.users OWNER TO jis;

--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO jis;

--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: wisata; Type: TABLE; Schema: public; Owner: jis
--

CREATE TABLE public.wisata (
    id bigint NOT NULL,
    kategori_id bigint NOT NULL,
    nama character varying(100) NOT NULL,
    deskripsi text,
    alamat character varying(255),
    lat numeric(10,7) NOT NULL,
    lng numeric(10,7) NOT NULL,
    harga_tiket numeric(12,0) DEFAULT '0'::numeric NOT NULL,
    jam_buka time(0) without time zone,
    jam_tutup time(0) without time zone,
    rating numeric(2,1) DEFAULT '0'::numeric NOT NULL,
    foto text,
    status_aktif boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    telepon character varying(30),
    status_operasional character varying(30) DEFAULT 'normal'::character varying NOT NULL,
    catatan_status text
);


ALTER TABLE public.wisata OWNER TO jis;

--
-- Name: wisata_id_seq; Type: SEQUENCE; Schema: public; Owner: jis
--

CREATE SEQUENCE public.wisata_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.wisata_id_seq OWNER TO jis;

--
-- Name: wisata_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: jis
--

ALTER SEQUENCE public.wisata_id_seq OWNED BY public.wisata.id;


--
-- Name: chat_messages id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.chat_messages ALTER COLUMN id SET DEFAULT nextval('public.chat_messages_id_seq'::regclass);


--
-- Name: chat_sessions id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.chat_sessions ALTER COLUMN id SET DEFAULT nextval('public.chat_sessions_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: kategori id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.kategori ALTER COLUMN id SET DEFAULT nextval('public.kategori_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: wisata id; Type: DEFAULT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.wisata ALTER COLUMN id SET DEFAULT nextval('public.wisata_id_seq'::regclass);


--
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- Data for Name: chat_messages; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.chat_messages (id, session_id, role, pesan, intent_json, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: chat_sessions; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.chat_sessions (id, session_token, lat, lng, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- Data for Name: kategori; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.kategori (id, nama, created_at, updated_at) FROM stdin;
1	Pantai	2026-09-20 00:21:25	2026-09-20 00:21:25
2	Pulau	2026-09-20 00:21:25	2026-09-20 00:21:25
3	Alam	2026-09-20 00:21:25	2026-09-20 00:21:25
4	Museum	2026-09-20 00:21:25	2026-09-20 00:21:25
5	Sejarah	2026-09-20 00:21:25	2026-09-20 00:21:25
6	Kuliner	2026-09-20 00:21:25	2026-09-20 00:21:25
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_09_04_223100_create_kategori_table	1
5	2026_09_04_223200_create_wisata_table	1
6	2026_09_04_223300_create_chat_sessions_table	1
7	2026_09_04_223400_create_chat_messages_table	1
8	2026_09_04_223500_add_role_to_users_table	1
9	2026_09_06_020000_add_telepon_to_wisata_table	1
10	2026_09_06_030000_alter_foto_to_text_in_wisata_table	1
11	2026_09_07_000001_add_status_operasional_to_wisata_table	1
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role) FROM stdin;
\.


--
-- Data for Name: wisata; Type: TABLE DATA; Schema: public; Owner: jis
--

COPY public.wisata (id, kategori_id, nama, deskripsi, alamat, lat, lng, harga_tiket, jam_buka, jam_tutup, rating, foto, status_aktif, created_at, updated_at, telepon, status_operasional, catatan_status) FROM stdin;
1	1	Pantai Air Manis	Pantai ikonik Padang dengan legenda Batu Malin Kundang. Cocok untuk keluarga, tersedia warung seafood dan area bermain.	Jl. Raya Air Manis, Kec. Padang Selatan, Kota Padang	-0.9746000	100.3626000	10000	07:00:00	18:30:00	4.5	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230284c7%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%230c4a6e%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2240%22%20r%3D%2214%22%20fill%3D%22%23fed7aa%22%2F%3E%3Cpath%20d%3D%22M18%2078%20C%2034%2068%2C%2050%2068%2C%2066%2078%20C%2082%2088%2C%2098%2088%2C%20106%2082%22%20fill%3D%22none%22%20stroke%3D%22%23ffffff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M14%2092%20C%2030%2082%2C%2046%2082%2C%2062%2092%20C%2078%20102%2C%2094%20102%2C%20106%2096%22%20fill%3D%22none%22%20stroke%3D%22%237dd3fc%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	0751-23456	normal	\N
2	1	Pantai Padang	Pantai kota sepanjang Teluk Sumatera. Landmark Jembatan Siti Nurbaya terlihat jelas, jogging track sepanjang 2 km.	Jl. Samudera, Kec. Padang Barat, Kota Padang	-0.9489000	100.3572000	0	06:00:00	22:00:00	4.3	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230284c7%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%230c4a6e%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2240%22%20r%3D%2214%22%20fill%3D%22%23fed7aa%22%2F%3E%3Cpath%20d%3D%22M18%2078%20C%2034%2068%2C%2050%2068%2C%2066%2078%20C%2082%2088%2C%2098%2088%2C%20106%2082%22%20fill%3D%22none%22%20stroke%3D%22%23ffffff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M14%2092%20C%2030%2082%2C%2046%2082%2C%2062%2092%20C%2078%20102%2C%2094%20102%2C%20106%2096%22%20fill%3D%22none%22%20stroke%3D%22%237dd3fc%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
3	1	Pantai Nirwana	Pantai bersih di Teluk Bayur, populer untuk sunset. Ada gazebo dan pelelangan ikan tradisional.	Jl. Padang Panjang, Kec. Padang Selatan	-0.9854000	100.3611000	5000	08:00:00	19:00:00	4.4	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230284c7%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%230c4a6e%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2240%22%20r%3D%2214%22%20fill%3D%22%23fed7aa%22%2F%3E%3Cpath%20d%3D%22M18%2078%20C%2034%2068%2C%2050%2068%2C%2066%2078%20C%2082%2088%2C%2098%2088%2C%20106%2082%22%20fill%3D%22none%22%20stroke%3D%22%23ffffff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M14%2092%20C%2030%2082%2C%2046%2082%2C%2062%2092%20C%2078%20102%2C%2094%20102%2C%20106%2096%22%20fill%3D%22none%22%20stroke%3D%22%237dd3fc%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
4	1	Pantai Carolina	Pantai landai dengan ombak tenang, cocok untuk anak-anak. Tersedia penyewaan ban dan pelampung.	Kec. Bungus Teluk Kabung, Kota Padang	-1.0430000	100.3986000	10000	07:30:00	18:00:00	4.2	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230284c7%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%230c4a6e%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2240%22%20r%3D%2214%22%20fill%3D%22%23fed7aa%22%2F%3E%3Cpath%20d%3D%22M18%2078%20C%2034%2068%2C%2050%2068%2C%2066%2078%20C%2082%2088%2C%2098%2088%2C%20106%2082%22%20fill%3D%22none%22%20stroke%3D%22%23ffffff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M14%2092%20C%2030%2082%2C%2046%2082%2C%2062%2092%20C%2078%20102%2C%2094%20102%2C%20106%2096%22%20fill%3D%22none%22%20stroke%3D%22%237dd3fc%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
5	1	Pantai Pasir Jambak	Destinasi wisata keluarga dengan waterboom, perahu banana boat, dan arena bermain pasir.	Jl. Pasir Jambak, Kec. Padang Utara	-0.9166000	100.3480000	15000	08:00:00	19:00:00	4.4	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230284c7%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%230c4a6e%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2260%22%20cy%3D%2240%22%20r%3D%2214%22%20fill%3D%22%23fed7aa%22%2F%3E%3Cpath%20d%3D%22M18%2078%20C%2034%2068%2C%2050%2068%2C%2066%2078%20C%2082%2088%2C%2098%2088%2C%20106%2082%22%20fill%3D%22none%22%20stroke%3D%22%23ffffff%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M14%2092%20C%2030%2082%2C%2046%2082%2C%2062%2092%20C%2078%20102%2C%2094%20102%2C%20106%2096%22%20fill%3D%22none%22%20stroke%3D%22%237dd3fc%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	0751-442288	normal	\N
6	2	Pulau Sikuai	Pulau resort 15 menit dari kota. Vila di atas air, spot snorkeling dan diving, serta trail mangrove. Resort utama: Sikuai Island Resort.	Kepulauan Bungus, Kota Padang	-1.1650000	100.3510000	250000	07:00:00	17:00:00	4.6	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230891b2%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23164e63%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M16%2094%20Q%2060%2076%20104%2094%22%20fill%3D%22%23fde68a%22%20stroke%3D%22%23f59e0b%22%20stroke-width%3D%222%22%2F%3E%3Cpath%20d%3D%22M60%2084%20Q%2054%2050%2066%2036%22%20fill%3D%22none%22%20stroke%3D%22%2378350f%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M66%2036%20Q%2040%2030%2032%2044%20M%2066%2036%20Q%2048%2016%2058%2012%20M%2066%2036%20Q%2084%2016%2094%2026%20M%2066%2036%20Q%2092%2034%2084%2048%22%20fill%3D%22none%22%20stroke%3D%22%2322c55e%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	0751-466333	normal	\N
7	2	Pulau Setan Lokang	Pulau kecil tak berpenduduk, spot diving favorit dengan visibility 15-25 meter. Akses via Bungus.	Selat Sunda Kecil, Bungus Teluk Kabung	-1.1234000	100.3567000	0	07:00:00	17:00:00	4.5	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230891b2%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23164e63%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M16%2094%20Q%2060%2076%20104%2094%22%20fill%3D%22%23fde68a%22%20stroke%3D%22%23f59e0b%22%20stroke-width%3D%222%22%2F%3E%3Cpath%20d%3D%22M60%2084%20Q%2054%2050%2066%2036%22%20fill%3D%22none%22%20stroke%3D%22%2378350f%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M66%2036%20Q%2040%2030%2032%2044%20M%2066%2036%20Q%2048%2016%2058%2012%20M%2066%2036%20Q%2084%2016%2094%2026%20M%2066%2036%20Q%2092%2034%2084%2048%22%20fill%3D%22none%22%20stroke%3D%22%2322c55e%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
8	2	Pulau Pisang Gantung	Gugusan pulau karang dengan laut jernih, favorit fotografer bawah air. Resort sederhana tersedia.	Kec. Bungus Teluk Kabung	-1.1950000	100.3725000	50000	07:00:00	18:00:00	4.3	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%230891b2%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23164e63%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M16%2094%20Q%2060%2076%20104%2094%22%20fill%3D%22%23fde68a%22%20stroke%3D%22%23f59e0b%22%20stroke-width%3D%222%22%2F%3E%3Cpath%20d%3D%22M60%2084%20Q%2054%2050%2066%2036%22%20fill%3D%22none%22%20stroke%3D%22%2378350f%22%20stroke-width%3D%225%22%20stroke-linecap%3D%22round%22%2F%3E%3Cpath%20d%3D%22M66%2036%20Q%2040%2030%2032%2044%20M%2066%2036%20Q%2048%2016%2058%2012%20M%2066%2036%20Q%2084%2016%2094%2026%20M%2066%2036%20Q%2092%2034%2084%2048%22%20fill%3D%22none%22%20stroke%3D%22%2322c55e%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
9	3	Lubuk Hitam	Air terjun 3 tingkat dengan kolam alami berwarna gelap (kesan mistis). Tracking 30 menit dari parkir.	Kec. Kuranji, Kota Padang	-0.8810000	100.3820000	5000	07:00:00	17:30:00	4.4	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23059669%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23064e3b%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpolygon%20points%3D%2224%2C92%2056%2C38%2088%2C92%22%20fill%3D%22%2315803d%22%2F%3E%3Cpolygon%20points%3D%2252%2C92%2078%2C46%20104%2C92%22%20fill%3D%22%23166534%22%2F%3E%3Cpolygon%20points%3D%2256%2C38%2048%2C52%2064%2C52%22%20fill%3D%22%23dcfce7%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
10	3	Bukit Nobita	Bukit dengan view kota Padang + laut. Spot sunset favorit, warung kopi di puncak.	Kec. Padang Barat	-0.9520000	100.3640000	0	06:00:00	22:00:00	4.5	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23059669%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23064e3b%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpolygon%20points%3D%2224%2C92%2056%2C38%2088%2C92%22%20fill%3D%22%2315803d%22%2F%3E%3Cpolygon%20points%3D%2252%2C92%2078%2C46%20104%2C92%22%20fill%3D%22%23166534%22%2F%3E%3Cpolygon%20points%3D%2256%2C38%2048%2C52%2064%2C52%22%20fill%3D%22%23dcfce7%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
11	3	Bukit Lampu	Bukit sederhana di pinggir kota, view pulau-pulau kecil. Cocok untuk anak-anak & keluarga.	Kec. Padang Selatan	-0.9710000	100.3690000	0	06:00:00	20:00:00	4.2	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23059669%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23064e3b%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpolygon%20points%3D%2224%2C92%2056%2C38%2088%2C92%22%20fill%3D%22%2315803d%22%2F%3E%3Cpolygon%20points%3D%2252%2C92%2078%2C46%20104%2C92%22%20fill%3D%22%23166534%22%2F%3E%3Cpolygon%20points%3D%2256%2C38%2048%2C52%2064%2C52%22%20fill%3D%22%23dcfce7%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
12	3	Taman Hutan Raya Bung Hatta	Taman konservasi dengan jembatan kanopi tertinggi di Indonesia, air terjun, dan arboretum 1.500 spesies.	Kec. Lubuk Kilangan, Kota Padang	-0.9625000	100.4530000	15000	07:00:00	17:00:00	4.7	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23059669%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23064e3b%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpolygon%20points%3D%2224%2C92%2056%2C38%2088%2C92%22%20fill%3D%22%2315803d%22%2F%3E%3Cpolygon%20points%3D%2252%2C92%2078%2C46%20104%2C92%22%20fill%3D%22%23166534%22%2F%3E%3Cpolygon%20points%3D%2256%2C38%2048%2C52%2064%2C52%22%20fill%3D%22%23dcfce7%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	0751-775555	normal	\N
13	4	Museum Adityawarman	Museum provinsi dengan arsitektur Rumah Gadang. Koleksi budaya Minangkabau, artefak prasejarah, batik.	Jl. Diponegoro No.10, Padang Barat	-0.9621000	100.3617000	5000	08:00:00	16:00:00	4.3	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23b45309%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23451a03%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpolygon%20points%3D%2260%2C24%2020%2C44%20100%2C44%22%20fill%3D%22%23fef3c7%22%2F%3E%3Crect%20x%3D%2222%22%20y%3D%2244%22%20width%3D%2276%22%20height%3D%226%22%20rx%3D%222%22%20fill%3D%22%23fde68a%22%2F%3E%3Crect%20x%3D%2228%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2246%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2266%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2284%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2218%22%20y%3D%2286%22%20width%3D%2284%22%20height%3D%228%22%20rx%3D%222%22%20fill%3D%22%23fde68a%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	0751-23200	normal	\N
14	4	Museum Situs Rumah Bersejarah	Kumpulan rumah kolonial Belanda abad 19, saksi sejarah Kota Padang tempo dulu.	Jl. Kota Tua, Padang Barat	-0.9605000	100.3580000	0	08:00:00	17:00:00	4.0	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23b45309%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23451a03%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpolygon%20points%3D%2260%2C24%2020%2C44%20100%2C44%22%20fill%3D%22%23fef3c7%22%2F%3E%3Crect%20x%3D%2222%22%20y%3D%2244%22%20width%3D%2276%22%20height%3D%226%22%20rx%3D%222%22%20fill%3D%22%23fde68a%22%2F%3E%3Crect%20x%3D%2228%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2246%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2266%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2284%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2218%22%20y%3D%2286%22%20width%3D%2284%22%20height%3D%228%22%20rx%3D%222%22%20fill%3D%22%23fde68a%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
15	4	Rumah Gadang Pallindo	Rumah adat Minangkabau asli dengan ukiran khas, terbuka untuk umum sebagai museum mini.	Jl. Sutan Sjahrir, Padang Selatan	-0.9650000	100.3680000	5000	09:00:00	17:00:00	4.1	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23b45309%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%23451a03%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpolygon%20points%3D%2260%2C24%2020%2C44%20100%2C44%22%20fill%3D%22%23fef3c7%22%2F%3E%3Crect%20x%3D%2222%22%20y%3D%2244%22%20width%3D%2276%22%20height%3D%226%22%20rx%3D%222%22%20fill%3D%22%23fde68a%22%2F%3E%3Crect%20x%3D%2228%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2246%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2266%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2284%22%20y%3D%2250%22%20width%3D%228%22%20height%3D%2236%22%20rx%3D%222%22%20fill%3D%22%23fff%22%2F%3E%3Crect%20x%3D%2218%22%20y%3D%2286%22%20width%3D%2284%22%20height%3D%228%22%20rx%3D%222%22%20fill%3D%22%23fde68a%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
16	5	Jembatan Siti Nurbaya	Jembatan ikonik Padang di muara Batang Arau, latar novel Marah Rusli. Spot sunset terbaik.	Jl. Padang-Teluk Bayur	-0.9589000	100.3594000	0	00:00:00	23:59:00	4.4	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%234f46e5%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%231e1b4b%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M22%2092%20L22%2046%20L38%2032%20L54%2046%20L54%2092%20Z%22%20fill%3D%22%23e0e7ff%22%2F%3E%3Cpath%20d%3D%22M66%2092%20L66%2046%20L82%2032%20L98%2046%20L98%2092%20Z%22%20fill%3D%22%23c7d2fe%22%2F%3E%3Crect%20x%3D%2242%22%20y%3D%2254%22%20width%3D%2236%22%20height%3D%2238%22%20rx%3D%222%22%20fill%3D%22%23818cf8%22%2F%3E%3Cpath%20d%3D%22M50%2092%20A10%2010%200%200%201%2070%2092%22%20fill%3D%22%231e1b4b%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
17	5	Masjid Raya Ganting	Masjid bersejarah peninggalan abad ke-19, arsitektur Minangkabau klasik, masih aktif untuk ibadah.	Jl. Ganting, Padang Barat	-0.9570000	100.3575000	0	04:30:00	21:00:00	4.5	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%234f46e5%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%231e1b4b%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M22%2092%20L22%2046%20L38%2032%20L54%2046%20L54%2092%20Z%22%20fill%3D%22%23e0e7ff%22%2F%3E%3Cpath%20d%3D%22M66%2092%20L66%2046%20L82%2032%20L98%2046%20L98%2092%20Z%22%20fill%3D%22%23c7d2fe%22%2F%3E%3Crect%20x%3D%2242%22%20y%3D%2254%22%20width%3D%2236%22%20height%3D%2238%22%20rx%3D%222%22%20fill%3D%22%23818cf8%22%2F%3E%3Cpath%20d%3D%22M50%2092%20A10%2010%200%200%201%2070%2092%22%20fill%3D%22%231e1b4b%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
18	5	Tugu Adipura	Tugu peringatan kota Padang, sering jadi latar foto. Dikelilingi taman kota kecil.	Jl. Permindo, Padang Barat	-0.9558000	100.3645000	0	06:00:00	22:00:00	3.9	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%234f46e5%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%231e1b4b%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M22%2092%20L22%2046%20L38%2032%20L54%2046%20L54%2092%20Z%22%20fill%3D%22%23e0e7ff%22%2F%3E%3Cpath%20d%3D%22M66%2092%20L66%2046%20L82%2032%20L98%2046%20L98%2092%20Z%22%20fill%3D%22%23c7d2fe%22%2F%3E%3Crect%20x%3D%2242%22%20y%3D%2254%22%20width%3D%2236%22%20height%3D%2238%22%20rx%3D%222%22%20fill%3D%22%23818cf8%22%2F%3E%3Cpath%20d%3D%22M50%2092%20A10%2010%200%200%201%2070%2092%22%20fill%3D%22%231e1b4b%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
19	6	Rumah Makan Sederhana	Restoran Padang legendaris, wajib coba: rendang, gulai tunjang, sate padang. Buka 24 jam.	Jl. Kwini No.10, Padang Barat	-0.9605000	100.3680000	35000	00:00:00	23:59:00	4.6	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23e11d48%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%234c0519%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M30%2068%20A30%2030%200%200%201%2090%2068%20Z%22%20fill%3D%22%23ffe4e6%22%2F%3E%3Crect%20x%3D%2222%22%20y%3D%2268%22%20width%3D%2276%22%20height%3D%226%22%20rx%3D%223%22%20fill%3D%22%23fda4af%22%2F%3E%3Ccircle%20cx%3D%2260%22%20cy%3D%2234%22%20r%3D%225%22%20fill%3D%22%23fda4af%22%2F%3E%3Cline%20x1%3D%2220%22%20y1%3D%2284%22%20x2%3D%22100%22%20y2%3D%2284%22%20stroke%3D%22%23fecdd3%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	0751-22344	normal	\N
20	6	Warung Soto Padang	Soto padang khas (daging sapi + bihun + perkedel). Tersebar di beberapa cabang di kota.	Jl. HOS Cokroaminoto, Padang	-0.9620000	100.3700000	18000	07:00:00	22:00:00	4.4	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23e11d48%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%234c0519%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M30%2068%20A30%2030%200%200%201%2090%2068%20Z%22%20fill%3D%22%23ffe4e6%22%2F%3E%3Crect%20x%3D%2222%22%20y%3D%2268%22%20width%3D%2276%22%20height%3D%226%22%20rx%3D%223%22%20fill%3D%22%23fda4af%22%2F%3E%3Ccircle%20cx%3D%2260%22%20cy%3D%2234%22%20r%3D%225%22%20fill%3D%22%23fda4af%22%2F%3E%3Cline%20x1%3D%2220%22%20y1%3D%2284%22%20x2%3D%22100%22%20y2%3D%2284%22%20stroke%3D%22%23fecdd3%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
21	6	Pondok Mie Kocok Bandung	Mie kocok legendaris dengan kuah kaldu sapi pekat, racikan khas Minang.	Jl. Veteran, Padang	-0.9580000	100.3630000	22000	10:00:00	21:00:00	4.3	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23e11d48%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%234c0519%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M30%2068%20A30%2030%200%200%201%2090%2068%20Z%22%20fill%3D%22%23ffe4e6%22%2F%3E%3Crect%20x%3D%2222%22%20y%3D%2268%22%20width%3D%2276%22%20height%3D%226%22%20rx%3D%223%22%20fill%3D%22%23fda4af%22%2F%3E%3Ccircle%20cx%3D%2260%22%20cy%3D%2234%22%20r%3D%225%22%20fill%3D%22%23fda4af%22%2F%3E%3Cline%20x1%3D%2220%22%20y1%3D%2284%22%20x2%3D%22100%22%20y2%3D%2284%22%20stroke%3D%22%23fecdd3%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
22	6	Pasar Raya Padang	Pusat oleh-oleh khas Padang: rendang kemasan, kerupuk, dan kue basah. Buka sejak subuh.	Jl. M. Yamin, Padang Barat	-0.9610000	100.3575000	0	05:00:00	20:00:00	4.5	data:image/svg+xml;utf8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20120%20120%22%20width%3D%22120%22%20height%3D%22120%22%3E%0A%20%20%20%20%3Cdefs%3E%0A%20%20%20%20%20%20%20%20%3ClinearGradient%20id%3D%22g%22%20x1%3D%220%25%22%20y1%3D%220%25%22%20x2%3D%22100%25%22%20y2%3D%22100%25%22%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%220%25%22%20stop-color%3D%22%23e11d48%22%2F%3E%0A%20%20%20%20%20%20%20%20%20%20%20%20%3Cstop%20offset%3D%22100%25%22%20stop-color%3D%22%234c0519%22%2F%3E%0A%20%20%20%20%20%20%20%20%3C%2FlinearGradient%3E%0A%20%20%20%20%3C%2Fdefs%3E%0A%20%20%20%20%3Crect%20width%3D%22120%22%20height%3D%22120%22%20rx%3D%2224%22%20fill%3D%22url%28%23g%29%22%2F%3E%0A%20%20%20%20%3Ccircle%20cx%3D%2295%22%20cy%3D%2225%22%20r%3D%2230%22%20fill%3D%22rgba%28255%2C255%2C255%2C0.06%29%22%2F%3E%0A%20%20%20%20%3Cpath%20d%3D%22M30%2068%20A30%2030%200%200%201%2090%2068%20Z%22%20fill%3D%22%23ffe4e6%22%2F%3E%3Crect%20x%3D%2222%22%20y%3D%2268%22%20width%3D%2276%22%20height%3D%226%22%20rx%3D%223%22%20fill%3D%22%23fda4af%22%2F%3E%3Ccircle%20cx%3D%2260%22%20cy%3D%2234%22%20r%3D%225%22%20fill%3D%22%23fda4af%22%2F%3E%3Cline%20x1%3D%2220%22%20y1%3D%2284%22%20x2%3D%22100%22%20y2%3D%2284%22%20stroke%3D%22%23fecdd3%22%20stroke-width%3D%224%22%20stroke-linecap%3D%22round%22%2F%3E%0A%3C%2Fsvg%3E	t	2026-09-20 00:21:25	2026-09-20 00:21:25	\N	normal	\N
\.


--
-- Name: chat_messages_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.chat_messages_id_seq', 2, true);


--
-- Name: chat_sessions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.chat_sessions_id_seq', 1, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: kategori_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.kategori_id_seq', 6, true);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.migrations_id_seq', 11, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.users_id_seq', 1, false);


--
-- Name: wisata_id_seq; Type: SEQUENCE SET; Schema: public; Owner: jis
--

SELECT pg_catalog.setval('public.wisata_id_seq', 22, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: chat_messages chat_messages_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.chat_messages
    ADD CONSTRAINT chat_messages_pkey PRIMARY KEY (id);


--
-- Name: chat_sessions chat_sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.chat_sessions
    ADD CONSTRAINT chat_sessions_pkey PRIMARY KEY (id);


--
-- Name: chat_sessions chat_sessions_session_token_unique; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.chat_sessions
    ADD CONSTRAINT chat_sessions_session_token_unique UNIQUE (session_token);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: kategori kategori_nama_unique; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.kategori
    ADD CONSTRAINT kategori_nama_unique UNIQUE (nama);


--
-- Name: kategori kategori_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.kategori
    ADD CONSTRAINT kategori_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: wisata wisata_pkey; Type: CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.wisata
    ADD CONSTRAINT wisata_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: chat_messages_session_id_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX chat_messages_session_id_index ON public.chat_messages USING btree (session_id);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: wisata_kategori_id_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX wisata_kategori_id_index ON public.wisata USING btree (kategori_id);


--
-- Name: wisata_status_aktif_index; Type: INDEX; Schema: public; Owner: jis
--

CREATE INDEX wisata_status_aktif_index ON public.wisata USING btree (status_aktif);


--
-- Name: chat_messages chat_messages_session_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.chat_messages
    ADD CONSTRAINT chat_messages_session_id_foreign FOREIGN KEY (session_id) REFERENCES public.chat_sessions(id) ON DELETE CASCADE;


--
-- Name: wisata wisata_kategori_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: jis
--

ALTER TABLE ONLY public.wisata
    ADD CONSTRAINT wisata_kategori_id_foreign FOREIGN KEY (kategori_id) REFERENCES public.kategori(id) ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

\unrestrict FxXsfOKgJp27SrndtEKfLnyBWcCHztwJ4tARhKepfcOUMQUUm8sciHRtScmdWyh

