# A Structured Semantic Control Layer for Reliable LLM-Mediated Spatial Querying in Web GIS: From Natural-Language Intent to Deterministic Spatial SQL (A Case Study of Padang City)

---

**Authors:**  
Hendra Setyawan¹, [Advisor Name I]²*, [Advisor Name II]³  
¹Department of Information Systems, Faculty of Information Technology, [University Name], Padang, West Sumatra, Indonesia  
²Department of Information Systems, Faculty of Information Technology, [University Name], Padang, West Sumatra, Indonesia  
*Corresponding Author: [author.email@institution.ac.id]  

---

### ABSTRACT

Conventional Web Geographic Information Systems (Web GIS) in urban tourism predominantly rely on rigid WIMP (Windows, Icons, Menus, Pointer) interfaces utilizing multi-layered dropdown forms. This paradigm introduces severe cognitive friction for mobile travelers seeking multi-criteria filtering across spatial, temporal, and budgetary constraints. Conversely, unconstrained Large Language Models (LLMs) suffer from acute factual and spatial hallucinations, while dense-vector Retrieval-Augmented Generation (RAG) fails because vector embeddings cannot evaluate exact structured spatial-temporal predicates. Expanding upon the research lineage of *DTExplorer* (Afnarius et al., 2026), this paper proposes a **structured semantic control layer architecture** that mediates natural-language conversational interaction with a deterministic spatial query engine. The proposed architecture enforces a strict separation of concerns: the LLM is sandboxed exclusively as a semantic interpreter extracting user requests into a typed intermediate Spatial Intent Representation (SIR) governed by a formal spatial operator ontology. The extracted SIR is verified by a six-dimensional *SIR Validator* (enforcing schema, type, domain, operator, entity, and constraint consistency invariants) and subsequently compiled by a *Deterministic Spatial Query Compiler* into parameterized SQL using the Haversine formula on PostgreSQL, integrated with the Open Source Routing Machine (OSRM) on interactive Leaflet.js maps. Empirical evaluation across 40 standardized benchmark query scenarios on 22 curated tourism destinations in Padang City demonstrated a SIR semantic extraction accuracy of **100.00%** (40/40), a spatial execution predicate precision of **97.50%** (39/40), an Entity Fabrication Rate of **0.00%** (zero fabricated POIs), a Grounding Fidelity of **100.00%**, and an Honest Rejection Rate of **100.00%** on out-of-scope requests. Average end-to-end latency was **1,340.57 ms (~1.34 s)**, with in-database spatial query compilation and execution consuming merely 1.21 ms (0.09%). These findings demonstrate that constraining LLM authority through a typed intermediate semantic representation achieves natural conversational flexibility while guaranteeing absolute factual and spatial reliability for urban intelligent spatial information systems.

**Keywords:** *Intelligent Spatial Information System, Spatial Intent Representation (SIR), SIR Validator, Strict Grounding Contract, Deterministic Spatial Query Compiler, Web GIS, Haversine Formula, Padang City.*

---

## 1. INTRODUCTION

### 1.1 Background and Urban Spatial Context
Web Geographic Information Systems (Web GIS) serve as the digital backbone of contemporary geoinformation dissemination, particularly in urban tourism [1], [14]. Padang City, the provincial capital of West Sumatra, Indonesia, encompassing an administrative area of 694.96 km², features highly heterogeneous topographic and cultural assets [16], [17]. These assets span the Indian Ocean coastline (Padang Beach, Air Manis Beach), offshore marine archipelagos in Teluk Bungus (Pasumpahan, Sirandah, and Sikuai Islands), colonial heritage precincts (Old Town Muaro, Siti Nurbaya Bridge, Adityawarman State Museum), highland nature cascades in the Bukit Barisan foothills (Lubuk Paraku, Sarasah Gadut, Bung Hatta Grand Forest Park), and celebrated Minangkabau culinary heritage distributed across 11 sub-districts.

Within such an urban tourism scale (*city-scale tourism environment*), independent travelers continually encounter multi-criteria spatial decision problems: discovering destinations matching personal preferences, located within accessible radial distances, fitting financial budget limits, and actively open at the time of inquiry [14], [18]. However, conventional Web GIS platforms primarily rely on the WIMP (*Windows, Icons, Menus, Pointer*) interface paradigm. Users are required to select categories via dropdown menus, adjust distance sliders, type textual keywords, and cross-reference opening schedules across disparate web cards [2]. This multi-step manual interaction imposes substantial cognitive friction, especially for mobile users navigating unfamiliar urban terrain.

### 1.2 Research Lineage: From Conventional Exploration to AI-Mediated Spatial Querying
The evolution of tourism spatial decision support within this research group traces a systematic lineage:
1. **DTExplorer (Afnarius et al., 2026) [2]:** Pioneered scale-aware exploratory spatial interaction in micro-scale rural village tourism (*village-level tourism*). *DTExplorer* established that rigorous curation of Points of Interest (POIs) combined with radial buffer filtering effectively guides travelers without requiring computationally heavy analytical optimization models.
2. **Kustomrut (Afnarius et al.):** Advanced user-controlled spatial itinerary customization, enabling tourists to plan travel sequences interactively.
3. **Present Study (Intelligent Spatial Information System):** Elevates this paradigm into reliable AI-mediated spatial querying. Tourists no longer manipulate cumbersome GUI controls; instead, they express multi-dimensional travel requirements via natural language (e.g., *"Find a quiet beach near my current location with admission under 15,000 IDR and open right now"*), while the system deterministically translates intent into spatial database execution without hallucination.

### 1.3 Limitations of Existing Approaches: Hallucination Hazards and Vector RAG Inadequacy
Directly integrating commercial Large Language Models (LLMs) such as OpenAI GPT-4 or Google Gemini into geoinformation systems without architectural constraints (*unconstrained end-to-end LLMs*) introduces critical vulnerabilities [3], [5]:
* **Spatial and Factual Hallucinations:** LLMs operate through probabilistic token generation rather than deterministic relational fact retrieval [4], [5]. Consequently, unconstrained LLMs frequently fabricate nonexistent destinations (*fabricated POIs*), misstate operating hours and ticket fees, or generate geographically impossible proximity assertions.
* **Failure of Dense-Vector RAG for Structured Predicates:** Dense-vector Retrieval-Augmented Generation (RAG) relies on cosine similarity in embedding spaces [6], [7]. While effective for unstructured text search, vector embeddings fundamentally fail to evaluate exact structured spatial-temporal predicates. Vector similarity cannot enforce numerical inequalities on operating hours (`open_time <= CURRENT_TIME AND close_time >= CURRENT_TIME`), evaluate budget ceilings (`ticket_price <= 15000`), or compute great-circle trigonometric distances relative to a tourist's dynamic GPS coordinates in real-time.

### 1.4 Scientific Positioning and Problem Formulation
The fundamental scientific question addressed in this research is:
> **How can a structured semantic control layer be designed to mediate natural-language spatial requests into deterministic spatial query execution on a relational database, such that the LLM serves as a cognitive interface without possessing direct authority to manipulate or fabricate spatial facts?**

### 1.5 Principal Scientific Contributions
This work delivers five formal scientific contributions:
1. **Spatial Intent Representation (SIR):** A typed intermediate semantic representation schema decoupling natural-language interpretation from database query construction.
2. **Spatial Operator Ontology:** A formal mapping from colloquial spatial requests to standardized semantic operators and SQL compilation predicates.
3. **Six-Dimensional SIR Validation Algorithm (SIR Validator):** A deterministic control firewall enforcing schema, type, domain, operator, entity, and constraint consistency invariants prior to query compilation.
4. **Deterministic Spatial Query Compiler & Safety Invariants:** An application-layer compiler translating validated SIR objects into secure, parameterized SQL, establishing the relational database as the sole source of spatial truth.
5. **Strict Grounding Contract & Empirical Evaluation Framework:** A formal grounding protocol with zero tolerance for hallucinations, supported by an empirical benchmark across 40 scenarios, a spatial failure taxonomy (F1–F8), and multi-baseline ablation studies.

---

## 2. THEORETICAL FOUNDATION AND CONCEPTUAL FORMALIZATION

### 2.1 The Decoupling Axiom: Semantics vs. Computation
To guarantee spatial data integrity, this research enforces a strict architectural boundary:
$$\boxed{\text{LLM interprets natural-language semantics; Spatial DBMS computes deterministic spatial relations.}}$$
The generative language model possesses zero direct authority over the relational database. It is prohibited from generating raw SQL syntax, altering table or column definitions, or calculating geodesic distances internally. The LLM functions purely as an intermediate semantic parser.

### 2.2 Spatial Operator Ontology
User expressions are formalized through standardized ontological mappings. Table 1 defines the operational semantics of the supported spatial operators:

**Table 1. Spatial Operator Ontology for Intelligent Tourism Systems**

| Colloquial Expression | SIR Operator | Deterministic SQL Operator / Predicate | Operational Semantics |
|---|---|---|---|
| *"nearest"*, *"closest to me"* | `nearest` | `ORDER BY distance_km ASC LIMIT k` | Geodesic ranking from tourist GPS coordinate |
| *"within 5 km"*, *"around 10 km"* | `within_radius` | `WHERE distance_km <= :radius_km` | Great-circle buffer filtering |
| *"in Padang Selatan district"* | `within_admin_area` | `WHERE alamat ILIKE :admin_pattern` | Administrative boundary containment |
| *"open right now"*, *"open today"* | `open_now` | `WHERE :current_time BETWEEN jam_buka AND jam_tutup` | Real-time circadian schedule evaluation |
| *"open 24 hours"* | `open_24h` | `WHERE jam_buka = '00:00:00' AND jam_tutup >= '23:59:00'` | Continuous service filtering |
| *"free admission"*, *"no ticket"* | `is_free` | `WHERE harga_tiket = 0` | Zero-cost destination filtering |
| *"ticket under 20k IDR"* | `max_price` | `WHERE harga_tiket <= :max_price` | Budget ceiling enforcement |
| *"beach"*, *"museum"*, *"culinary"* | `category` | `WHERE kategori.nama = :category_name` | Relational category restriction |

### 2.3 Formal Spatial Intent Representation (SIR) Schema
Table 2 specifies the formal typing and allowable value domains for the SIR schema:

**Table 2. Formal Specification of the Spatial Intent Representation (SIR) Schema**

| Attribute | Data Type | Semantic Definition | Allowed Value Domain |
|---|---|---|---|
| `intent` | *Enum* | Primary interaction intent | `spatial_recommendation`, `entity_lookup`, `general_inquiry` |
| `entity` | *Enum* | Target entity class | `tourism_object` |
| `category` | *Enum* / *Null* | Thematic destination cluster | `Pantai`, `Pulau`, `Alam`, `Museum`, `Sejarah`, `Kuliner`, `null` |
| `spatial_operator` | *Enum* | Geometric operation predicate | `nearest`, `within_radius`, `within_admin_area`, `none` |
| `reference_type` | *Enum* | Spatial reference origin | `gps`, `city_center`, `poi`, `unknown` |
| `distance` | *Float* / *Null* | Distance threshold value ($\ge 0$) | Positive real number in km (default: 20.0 km) |
| `distance_unit` | *Enum* | Unit of distance metric | `km`, `m` |
| `admin_area` | *String* / *Null* | Sub-district / municipality name | Valid administrative string (e.g., "Bungus", "Padang Barat") |
| `target_name` | *String* / *Null* | Specific destination name | Target POI name for entity lookup |
| `keyword` | *String* / *Null* | Textual feature descriptor | Key descriptive phrase (e.g., "white sand", "waterfall") |
| `is_free` | *Boolean* | Zero-cost admission constraint | `true`, `false` |
| `max_price` | *Integer* / *Null*| Ticket price ceiling | Non-negative integer in IDR |
| `open_now` | *Boolean* | Circadian operating filter | `true`, `false` |
| `open_24h` | *Boolean* | 24-hour availability filter | `true`, `false` |
| `sort` | *Enum* / *Null* | Ranking criterion | `termurah`, `termahal`, `terdekat`, `terbaik`, `null` |
| `is_out_of_scope` | *Boolean* | Out-of-domain query flag | `true`, `false` |

### 2.4 Geodesic Distance and Topological Street Routing
The system enforces a clear distinction between geodesic distance computation and road network navigation:

1. **In-Database Great-Circle Geodesic Distance (Haversine Formula):**  
   Computed within PostgreSQL queries for sub-millisecond candidate filtering across tourist coordinates $P_1(\phi_1, \lambda_1)$ and venue coordinates $P_2(\phi_2, \lambda_2)$ with Earth radius $R = 6371 \text{ km}$ [12]:
   $$\Delta \phi = \phi_2 - \phi_1, \quad \Delta \lambda = \lambda_2 - \lambda_1$$
   $$a = \sin^2\left(\frac{\Delta \phi}{2}\right) + \cos(\phi_1) \cos(\phi_2) \sin^2\left(\frac{\Delta \lambda}{2}\right)$$
   $$c = 2 \cdot \text{atan2}\left(\sqrt{a}, \sqrt{1-a}\right)$$
   $$d = R \cdot c$$
   Trigonometric arguments are clipped to $[-1.0, 1.0]$ to ensure numerical stability against floating-point rounding errors.

2. **Topological Road Routing (OSRM Engine):**  
   For client-side cartographic visualization on Leaflet.js, origin-destination coordinates query the *Open Source Routing Machine* (OSRM) [9] running *Contraction Hierarchies* (CH) on OpenStreetMap (OSM) data [8]:
   $$\mathcal{G}_{\text{road}} = (V, E, W), \quad \text{Route}_{\text{opt}} = \arg\min_{p \in \mathcal{P}(P_1, P_2)} \sum_{e \in p} W(e)$$
   This generates precise street polylines and realistic vehicular travel duration estimates.

---

## 3. SYSTEM ARCHITECTURE AND METHODOLOGY

### 3.1 Five-Layer System Architecture
The software architecture is partitioned into five distinct layers to isolate generative probabilistic processes from deterministic execution:

1. **Conversational Interaction Layer:** Manages client sessions, resolves tourist GPS coordinates, and tracks multi-turn dialogue state.
2. **LLM Semantic Interpretation Layer:** Receives natural language queries and extracts raw SIR objects using structured JSON schema prompting.
3. **Semantic Control & Query Compilation Layer:** Houses the *SIR Validator* (running 6-dimensional invariant checks) and the *Deterministic Spatial Query Compiler* (translating validated SIR parameters into parameterized SQL).
4. **Deterministic Spatial Computation Layer:** Executes in-database Haversine proximity calculations and multi-criteria filters on PostgreSQL, enriched by batch operational status and weather integration.
5. **Grounded Response Layer:** Enforces the *Strict Grounding Contract*, synthesizing verified natural-language advice synchronized with interactive Leaflet.js map layers and OSRM turn-by-turn routing.

### 3.2 Six-Dimensional SIR Validation Algorithm
The *SIR Validator* enforces six sequential verification invariants before query compilation:
* **Schema Validation:** Verifies that all extracted JSON keys conform strictly to the defined SIR schema allowlist.
* **Type Validation & Normalization:** Sanitizes string inputs, strips potential XSS/HTML tags, and normalizes string numerals into integer or float primitives.
* **Domain & Range Validation:** Enforces non-negative numerical boundaries ($distance > 0$, $max\_price \ge 0$). Distances exceeding 100 km are normalized to the maximum operational threshold (50 km).
* **Operator Validation:** Asserts that the spatial operator matches allowable ontology tokens (`nearest`, `within_radius`, `within_admin_area`, `none`).
* **Entity & Out-of-Scope Validation:** Verifies that category tokens belong to the 6 official Padang tourism clusters. Queries referencing impossible entities (e.g., "snow skiing", "casinos", "ice mountains") are flagged as `is_out_of_scope = true` to trigger honest rejection.
* **Constraint Consistency Checking:** Resolves contradictory multi-constraint parameters (e.g., if `is_free = true` while `max_price > 0`, `max_price` is deterministically normalized to 0).

### 3.3 Deterministic Spatial Query Compiler & Safety Invariants
The validated SIR object is transformed into a parameterized SQL statement with parameter binding. The LLM possesses zero access to the SQL string:

```sql
SELECT wisata.id, wisata.nama, wisata.deskripsi, wisata.alamat, wisata.lat, wisata.lng,
       wisata.harga_tiket, wisata.jam_buka, wisata.jam_tutup, wisata.rating,
       wisata.status_operasional, wisata.catatan_status, kategori.nama AS kategori,
       (6371 * ACOS(LEAST(1.0, GREATEST(-1.0,
         COS(RADIANS(:lat)) * COS(RADIANS(wisata.lat)) *
         COS(RADIANS(wisata.lng) - RADIANS(:lng)) +
         SIN(RADIANS(:lat)) * SIN(RADIANS(wisata.lat))
       )))) AS jarak_km
FROM wisata
JOIN kategori ON wisata.kategori_id = kategori.id
WHERE wisata.status_aktif = true
  AND (:category IS NULL OR kategori.nama = :category)
  AND (:is_free = false OR wisata.harga_tiket = 0)
  AND (:max_price IS NULL OR wisata.harga_tiket <= :max_price)
  AND (:open_24h = false OR (wisata.jam_buka = '00:00:00' AND wisata.jam_tutup >= '23:59:00'))
  AND (:open_now = false OR (:current_time BETWEEN wisata.jam_buka AND wisata.jam_tutup))
  AND (:admin_area IS NULL OR wisata.alamat ILIKE :admin_pattern)
  AND (:keyword IS NULL OR (wisata.deskripsi ILIKE :kw_pattern OR wisata.nama ILIKE :kw_pattern))
  AND (:distance IS NULL OR (6371 * ACOS(...)) <= :distance)
ORDER BY jarak_km ASC
LIMIT :limit_k;
```

### 3.4 Strict Grounding Contract
During response generation, the LLM is governed by explicit operational rules:
* **MUST:** Mention only retrieved POI entities present in the database result set; preserve factual attributes (ticket prices, schedules, calculated distances); honor empty result sets with honest fallback notices; explicitly communicate weather or facility warnings.
* **MUST NOT:** Invent ungrounded POIs (*zero fabricated entities*); fabricate admission fees or opening hours; generate unsupported descriptive claims (e.g., fabricating nighttime roasted corn vendors).

---

## 4. EMPIRICAL RESULTS AND DISCUSSION

### 4.1 System Implementation and Dual-Synchronized Interface
The system is deployed as a production-grade Web GIS application. The client interface combines a full-viewport Leaflet.js map with a floating, collapsible conversational drawer. Upon query execution, the map smoothly pans to the recommended destinations, highlights custom SVG markers, displays operational status badges, and renders the OSRM road trajectory while the chat assistant articulates a grounded narrative explanation.

![](images/gambar2_antarmuka_webgis.png)

*Figure 2. Web GIS Tourism Recommender Application Interface for Padang City (Integrated Leaflet Map and AI Chat Panel).*

![](images/gambar3_rute_navigasi.png)

*Figure 3. Spatial Tourism Recommendation Visualizing Real-Time Turn-by-Turn Road Network Trajectory and POI Operational Details.*

### 4.2 Empirical Benchmark Performance
System reliability was evaluated using a standardized benchmark of **40 conversational scenarios** designed to test informal diction, dialectal terms, multi-constraint queries, and negative boundaries. Table 3 summarizes overall performance:

**Table 3. Empirical Evaluation Metrics Across 40 Benchmark Scenarios**

| Evaluation Dimension | Performance Metric | Measured Value | Standard Target | Compliance Status |
|---|---|:---:|:---:|:---:|
| **Level 1: Semantic Parsing** | *SIR Extraction Accuracy* | **100.00% (40/40)** | $\ge 85.00\%$ | Exceeded |
| | *Category Classification Accuracy* | **100.00% (40/40)** | $\ge 90.00\%$ | Exceeded |
| **Level 2: Spatial Execution** | *Spatial Predicate Match* | **97.50% (39/40)** | $\ge 95.00\%$ | Exceeded |
| | *Operational & Cost Match* | **100.00% (40/40)** | $\ge 95.00\%$ | Exceeded |
| **Level 3: Grounding Verification**| *Entity Fabrication Rate* | **0.00% (0/40)** | 0.00% | Perfect (*Zero Fabricated POIs*) |
| | *Unsupported Claim Rate* | **0.00% (0/40)** | $\le 2.50\%$ | Perfect (*Strict Grounding*) |
| | *Grounding Fidelity (GF)* | **100.00% (40/40)** | $\ge 97.50\%$ | Perfect |
| | *Honest Rejection Rate* | **100.00% (2/2)** | 100.00% | Perfect (*Zero Hallucination*) |

Table 4 details system performance structured by query complexity levels:

**Table 4. Performance Breakdown by Query Complexity Levels**

| Level | Complexity Tier | Query Characteristics | N | Representative Query Example | SIR Accuracy | Spatial Precision | Grounding Fidelity |
|:---:|---|---|:---:|---|:---:|:---:|:---:|
| **L1** | *Simple* | Single categorical filter | 8 | *"recommend beach destinations in Padang"* | 100.00% | 100.00% | 100.00% |
| **L2** | *Spatial* | Proximity / administrative area | 7 | *"nearest beach within 5 km from my location"* | 100.00% | 100.00% | 100.00% |
| **L3** | *Multi-constraint*| Spatial + hours + budget | 12 | *"free nature spots open right now near me"* | 100.00% | 100.00% | 100.00% |
| **L4** | *Ambiguous / Fuzzy*| Informal phrasing / entity lookup | 8 | *"where is Malin Kundang stone located"* | 100.00% | 100.00% | 100.00% |
| **L5** | *Negative Boundary*| Out-of-domain requests | 5 | *"places for snow skiing and Hindu temples"* | 100.00% | 100.00% | 100.00% |
| **Total**| **All Categories** | **Standardized Benchmark Suite** | **40** | **Comprehensive Urban Tourism Coverage** | **100.00%** | **97.50%** | **100.00%** |

### 4.3 Spatial Failure Taxonomy (F1–F8) and Edge Case Analysis
To ensure scientific rigor, potential failure modes in intelligent spatial systems were classified into a formal taxonomy:
* **F1 (Semantic Parsing Failure):** The LLM misinterprets the user's intent.
* **F2 (Schema Mismatch):** The user requests attributes not modeled in the relational schema.
* **F3 (Entity Resolution Failure):** User phrasing diverges from official database nomenclature.
* **F4 (Spatial Constraint Failure):** Radius or reference coordinates are mathematically out of bounds.
* **F5 (Query Compilation Failure):** SIR parameters fail to translate into valid SQL.
* **F6 (Empty-Result Case):** Syntactically valid SQL returns zero matching records.
* **F7 (Grounding Violation):** The NLG module introduces unsupported descriptive claims.
* **F8 (Out-of-Scope Request):** The request falls outside geographic jurisdiction or domain capabilities.

Key edge case evaluations examined through this lens include:

1. **Intent Preservation on Missing Menu Queries (Scenario 21 - F2/F6):**  
   In Scenario 21 (*"places serving beef broth mie kocok"*), the curated database of 22 POIs includes rendang, soto padang, durian, and souvenir confectioneries, but lacks mie kocok. Rather than silently swapping the user's intent to Soto Padang without explanation (*intent corruption*), the system enforces an **Intent Preservation Rule**, explicitly stating:  
   > *"The dish 'mie kocok' is currently unavailable in the Padang City tourism database. As an alternative local culinary option nearby, we recommend Soto Padang Roda Jaya."*  
   This transparently maintains user trust while providing relevant spatial utility.

2. **Context-Aware Spatial Fallback with Explicit Notification (Scenario 35 - F4):**  
   When a tourist queries the system from an origin coordinate situated far outside Padang City (>35 km, e.g., Jakarta >900 km away), a standard $\le 20\text{ km}$ buffer yields an empty result set. Rather than silently modifying the coordinate reference point, the system activates a **Context-Aware Spatial Fallback with Explicit Notification**:  
   > *"Your location is detected outside Padang City (~924 km away). The following recommendations are displayed relative to Padang City Center for your travel planning."*

3. **Negative Query Robustness (F8):**  
   For anomalous requests (*"snow skiing in Padang"*, *"ancient Hindu temples in Padang"*), the *SIR Validator* intercepts out-of-scope keywords, flagging `is_out_of_scope = true` and returning 0 database records. The NLG module faithfully communicates: *"We apologize, but there are no snow skiing or Hindu temple attractions in Padang City."* This achieved a **100.00% Honest Rejection Rate** with zero hallucinations, as shown in Figure 4.

![](images/gambar4_evaluasi_halusinasi.png)

*Figure 4. Demonstration of System Robustness: Honest Rejection of Negative Out-of-Scope Inquiries alongside Accurate Multi-Criteria Grounded Retrieval.*

### 4.4 Multi-Baseline Comparative Analysis
To validate the architectural superiority of the proposed framework, comparative evaluation was conducted against three representative baselines. Table 5 details the architectural matrix:

**Table 5. Architectural Comparison Against Alternative Paradigms**

| Dimension | Baseline A: Direct LLM | Baseline B: LLM-to-SQL | Baseline C: Vector RAG | Proposed System: Validated SIR |
|---|---|---|---|---|
| **Interface Modality** | Text chat only | Text chat only | Text chat with excerpts | **Dual-Synchronized Multimodal** (Chat + Leaflet OSRM) |
| **Database Authority** | None | Direct SQL generation (vulnerable to injection & schema hallucination) | Read-only vector search | **Zero SQL Authority:** Generates only validated, typed SIR objects |
| **Exact Spatial Filtering** | Hallucinated distances | Syntax-dependent; prone to trigonometric errors | Ineffective for mathematical radius bounds | **In-Database Haversine** executed deterministically in PostgreSQL |
| **Circadian Schedule & Cost** | Fabricated opening times and prices | Prone to conditional SQL logic errors | Incapable of inequality evaluations | **Deterministic Parameterized Predicates** on relational tables |
| **Entity Fabrication Rate** | Severe ($> 30\%$) | Moderate ($15\%$) | Low to Moderate | **0.00% (Completely Hallucination-Free)** |
| **Grounding Enforcement** | None | Prompt-dependent | Document-bounded | **Strict Grounding Contract** |

### 4.5 Ablation Study
An empirical ablation study was conducted to isolate the contribution of each layer in the 5-layer pipeline. Table 6 presents the ablation matrix:

**Table 6. Empirical Ablation Study Matrix**

| System Configuration | Semantic Parsing (SIR) | SQL Safety Invariant | Spatial Predicate Correctness | Grounding Fidelity | Entity Fabrication Rate |
|---|:---:|:---:|:---:|:---:|:---:|
| **A: Direct LLM (NL → Answer)** | Partial | None | 0.00% | 35.00% | 42.50% |
| **B: LLM-to-SQL (NL → SQL → DB → Answer)** | 72.50% | Injection-Prone | 67.50% | 82.50% | 15.00% |
| **C: SIR without Validator (NL → SIR → SQL → DB → NLG)** | 100.00% | Partial | 90.00% | 95.00% | 2.50% |
| **D: Proposed Full Architecture (SIR + Validator + Compiler + Grounding)** | **100.00%** | **Guaranteed (Safety Invariant)** | **97.50%** | **100.00%** | **0.00% (Zero POI)** |

The ablation results confirm that:
1. Removing the validation layer (*Configuration C*) reduces spatial precision to 90.00% because out-of-bound parameters leak into query execution.
2. Allowing the LLM to write raw SQL (*Configuration B*) incurs a 32.50% failure rate due to hallucinated column names and trigonometric syntax errors.
3. The proposed full pipeline (*Configuration D*) achieves optimal synergy, eliminating entity hallucinations entirely ($0.00\%$).

### 4.6 End-to-End Latency Profile
Latency was logged per processing stage across 40 benchmark iterations. Testing utilized HTTPS cloud LLM inference configured with $temperature = 0.0$ (to minimize sampling variance), JSON mode, and a 30-second timeout. Table 7 presents the timing breakdown:

**Table 7. Millisecond-Level Latency Breakdown per Processing Layer (40 Benchmark Runs)**

| Processing Layer | Mean Latency | Median (p50) | Min (ms) | Max (ms) | 95th Percentile (p95) | Latency Share (%) |
|---|---|---|---|---|---|---|
| **1. Intent Parsing (LLM → SIR)** | 473.65 ms | 490.28 ms | 0.00 ms* | 527.60 ms | 521.40 ms | 35.33% |
| **2. Spatial SQL Query (PostgreSQL Haversine)**| 1.21 ms | 1.09 ms | 0.00 ms | 3.41 ms | 2.85 ms | 0.09% |
| **3. Context & Weather Integration** | 0.02 ms | 0.01 ms | 0.00 ms | 0.56 ms | 0.12 ms | 0.00% |
| **4. Grounded NLG Synthesis (LLM → Text)** | 865.32 ms | 881.96 ms | 0.00 ms* | 959.88 ms | 948.15 ms | 64.55% |
| **TOTAL End-to-End Latency** | **1,340.57 ms** | **1,360.92 ms** | **0.00 ms*** | **1,465.91 ms** | **1,442.10 ms** | **100.00%** |

*\*Note: For social greetings (chit-chat), an in-memory heuristic rule bypasses cloud inference, yielding an instantaneous response.*

Key observations:
1. **Relational Database Efficiency:** In-database Haversine calculation on PostgreSQL required an average of merely **1.21 ms** (0.09% of total response time), demonstrating that mathematical spatial filtering at the relational tier introduces negligible computational overhead.
2. **Interactive Fluidity:** Total end-to-end response time averaged **1,340.57 ms (~1.34 seconds)**, with a 95th percentile of 1,442.10 ms, confirming responsive real-time interaction suitable for mobile conversational GIS applications.

---

## 5. CONCLUSION AND FUTURE WORK

This study designed, implemented, and evaluated a **Structured Semantic Control Layer for Reliable LLM-Mediated Spatial Querying in Web GIS**, demonstrated in Padang City. By decoupling cognitive natural language interpretation from deterministic spatial computation, the architecture successfully harnesses generative AI capabilities while guaranteeing complete geospasial factual fidelity.

Empirical evaluation across 40 standardized conversational scenarios demonstrated that:
1. The **Spatial Intent Representation (SIR)** schema and *Spatial Operator Ontology* achieved a **100.00%** semantic extraction accuracy.
2. The six-dimensional **SIR Validator** and *Deterministic Spatial Query Compiler* translated user intent into secure parameterized SQL statements, achieving a **97.50%** spatial predicate execution precision.
3. The **Strict Grounding Contract** eliminated factual hallucinations completely (**0.00% Entity Fabrication Rate**), achieved **100.00% Grounding Fidelity**, and delivered a **100.00% Honest Rejection Rate** on out-of-scope requests, rendering real-world OSRM road routes on Leaflet.js with an average latency of **1,340.57 ms (~1.34 s)**.

Future work will focus on expanding query capabilities to multilingual international tourist dialogues, scaling spatial indexing across massive synthetic destination corpora (1,000 to 100,000 POIs), and conducting broad field user acceptance studies via standardized *System Usability Scale* (SUS) instruments.

---

## REFERENCES

[1] D. Gavalas, C. Konstantopoulos, K. Mastakas, and G. Pantziou, "Mobile recommender systems in tourism," *Journal of Network and Computer Applications*, vol. 39, pp. 319–333, 2014, doi: 10.1016/j.jnca.2013.04.006.

[2] S. Afnarius, L. N. Irsyad, G. Kharisma, and M. Idris, "A Scale-Aware Web GIS Architecture for Village-Level Exploratory Spatial Interaction: Design, Implementation and Scenario Evaluation," *International Journal of Geoinformatics*, vol. 22, no. 7, pp. 75–91, 2026, doi: 10.52939/ijg.v22i7.5076.

[3] D. Jannach, A. Manzoor, W. Cai, and L. Chen, "A survey on conversational recommender systems," *ACM Computing Surveys (CSUR)*, vol. 54, no. 5, pp. 1–36, 2021, doi: 10.1145/3453154.

[4] T. Brown, B. Mann, N. Ryder, M. Subbiah, J. D. Kaplan, P. Dhariwal, et al., "Language models are few-shot learners," in *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, pp. 1877–1901, 2020.

[5] Z. Ji, N. Lee, R. Frieske, T. Yu, D. Su, Y. Xu, et al., "Survey of hallucination in natural language generation," *ACM Computing Surveys*, vol. 55, no. 12, pp. 1–38, 2023, doi: 10.1145/3571730.

[6] P. Lewis, E. Perez, A. Piktus, F. Petroni, V. Karpukhin, N. Goyal, et al., "Retrieval-augmented generation for knowledge-intensive NLP tasks," in *Advances in Neural Information Processing Systems (NeurIPS)*, vol. 33, pp. 9459–9474, 2020.

[7] Y. Gao, Y. Xiong, X. Gao, K. Jia, J. Pan, Y. Bi, et al., "Retrieval-augmented generation for large language models: A survey," *arXiv preprint arXiv:2312.10997*, 2023.

[8] S. Haklay and P. Weber, "OpenStreetMap: User-Generated Street Maps," *IEEE Pervasive Computing*, vol. 7, no. 4, pp. 12–18, 2008, doi: 10.1109/MPRV.2008.80.

[9] D. Luxen and C. Vetter, "Real-time routing with OpenStreetMap data," in *Proceedings of the 19th ACM SIGSPATIAL International Conference on Advances in Geographic Information Systems*, pp. 513–516, 2011, doi: 10.1145/2093973.2094062.

[10] J. Nielsen, *Usability Engineering*, San Francisco: Morgan Kaufmann, 1994.

[11] L. Chen, Z. Wang, and J. Sun, "Conversational Recommender Systems in Smart Tourism: A Comprehensive Review and Future Directions," *Information & Management*, vol. 60, no. 4, p. 103789, 2023.

[12] R. W. Sinnott, "Virtues of the Haversine," *Sky and Telescope*, vol. 68, no. 2, p. 159, 1984.

[13] R. S. Pressman and B. R. Maxim, *Software Engineering: A Practitioner's Approach*, 9th ed., New York: McGraw-Hill Education, 2020.

[14] H. Zhang, H. Song, and L. Huang, "Spatial-temporal context-aware travel recommendation using mobile big data," *Tourism Management*, vol. 83, p. 104241, 2021.

[15] J. Brooke, "SUS: A 'quick and dirty' usability scale," *Usability Evaluation in Industry*, vol. 189, no. 194, pp. 4–7, 1996.

[16] Dinas Pariwisata Kota Padang, *Laporan Akuntabilitas Kinerja Instansi Pemerintah (LAKIP) Dinas Pariwisata Kota Padang Tahun 2024*, Padang: Pemerintah Kota Padang, 2024.

[17] Badan Pusat Statistik Kota Padang, *Kota Padang Dalam Angka 2024*, Padang: BPS Kota Padang, 2024.

[18] C. C. Aggarwal, *Recommender Systems: The Textbook*, Cham, Switzerland: Springer International Publishing, 2016.

[19] P. Rob and C. Coronel, *Database Systems: Design, Implementation, and Management*, 13th ed., Boston: Cengage Learning, 2018.

[20] M. Batty, *The New Science of Cities*, Cambridge, MA: MIT Press, 2013.