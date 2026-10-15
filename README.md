# ProiectPBD - Aplicatie de Gestiune a Proiectelor Europene

> **Sistem informatic pentru managementul ciclului de viață, pontajului și execuției financiare a proiectelor finanțate din fonduri europene (ex. PNRR, Horizon Europe, POCIDIF, Interreg) în cadrul companiilor de consultanță și servicii IT.**

---

## 1. Prezentare Generală și Obiective

### 1.1. Context și Problematică
Companiile de consultanță și dezvoltare software implicate în proiecte finanțate din fonduri europene gestionează proceduri administrative și financiare complexe:
- **Reguli rigide de eligibilitate financiară:** Încadrarea pe linii bugetare distincte, separarea cheltuielilor eligibile de cele neeligibile și monitorizarea cofinanțării.
- **Evidența strictă a resurselor umane:** Pontaj normat (fișe lunare de pontaj / timesheets), plafoane de ore pe zi și pe lună pentru fiecare expert, precum și corelarea activităților prestate cu pachetele de lucru (Work Packages) și livrabilele asumate.
- **Pregătirea cererilor de plată și rambursare:** Agregarea documentelor justificative (facturi, state de plată, rapoarte de activitate) conform cerințelor autorităților de management.
- **Prevenirea riscului de corecții financiare:** Reducerea erorilor umane în alocările financiare și evitarea suprapunerii orelor lucrate de experți pe proiecte multiple.

### 1.2. Obiectivele Aplicației
Sistemul își propune să ofere o soluție unificată pentru:
1. **Gestiunea proiectelor și a consorțiilor:** Evidența apelurilor de finanțare, a proiectelor contractate, a partenerilor și a rolurilor din cadrul consorțiilor.
2. **Planificarea operațională:** Structurarea pe pachete de lucru (Work Packages), activități, livrabile și jaloane (Milestones).
3. **Modulul de pontaj și conformitate:** Înregistrarea orelor de lucru cu verificarea automată a constrângerilor (evitarea suprapunerilor și depășirii normei zilnice/lunare).
4. **Monitorizare financiară și bugetară:** Alocarea pe capitole bugetare, urmărirea execuției financiare în timp real și pregătirea datelor necesare cererilor de rambursare.
5. **Piste de audit și raportare:** Menținerea istoricului modificărilor pentru conformitate și generarea rapoartelor de sinteză.

---

## 2. Etape de Implementare (Roadmap)

- [ ] **Etapa 1: Analiză, Documentație și Arhitectură**
  - Definirea cerințelor funcționale și non-funcționale.
  - Proiectarea modelului conceptual și logic al datelor (diagrama entitate-relație).
  - Elaborarea specificațiilor arhitecturale și a diagramelor de modelare structurală și comportamentală (cazuri de utilizare, clase, stări).

- [ ] **Etapa 2: Proiectarea și Implementarea Bazei de Date**
  - Crearea structurii de tabele, chei primare, chei străine și constrângeri de integritate.
  - Implementarea mecanismelor de validare la nivel de bază de date (proceduri și triggere pentru verificarea bugetelor și a pontajului).
  - Popularea bazei de date cu seturi de date consistente pentru scenarii de test.

- [ ] **Etapa 3: Dezvoltarea Serviciilor de Backend și Logică de Business**
  - Implementarea mecanismelor de autentificare, autorizare și control al accesului pe roluri (RBAC).
  - Implementarea modulelor de bază: gestiune proiecte, pachete de lucru, pontaj și bugete.
  - Aplicarea șabloanelor de proiectare adecvate pentru decuplarea componentelor și modularitate.

- [ ] **Etapa 4: Dezvoltarea Interfeței cu Utilizatorul**
  - Implementarea fluxurilor interactive de completare și aprobare a pontajelor.
  - Panou de control (dashboard) pentru vizualizarea indicatorilor de execuție bugetară și a stadiului livrabilelor.
  - Formulare dedicate pentru înregistrarea cheltuielilor și întocmirea cererilor de rambursare.

- [ ] **Etapa 5: Testare și Asigurarea Calității (QA)**
  - **Testare unitară:** Verificarea funcționării izolate a metodelor de calcul (rate orare, validări de eligibilitate).
  - **Testare de integrare:** Verificarea comunicării corecte între nivelul de date și serviciile aplicative.
  - **Testarea constrângerilor de date:** Validarea triggerelor și scenariilor-limită (ex: încercare de suprapunere pontaj, depășire plafon bugetar).
  - **Testare funcțională / End-to-End:** Simularea scenariilor reale parcurse de utilizatori (creare proiect, introducere pontaj expert, aprobare manager, generare raport).

- [ ] **Etapa 6: Documentație Finală și Livrare**
  - Finalizarea dosarului tehnic al proiectului (specificații software, raport de proiectare a bazei de date, raport de testare).
  - Pregătirea scenariilor demonstrative.
