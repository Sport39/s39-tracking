# S39Tracking

TikTok-Pixel, Meta-Pixel, Google Tag Manager und billiger.de/solute für sport39.de – nur nach Einwilligung im Cookie-Banner.

## Was das Plugin macht

- Meldet „TikTok Pixel“ und „Meta Pixel (Facebook/Instagram)“ als eigene Einträge im Cookie-Banner an (Gruppe einstellbar, Standard: Tracking).
- Lädt die Pixel nur, wenn der Besucher zugestimmt hat.
- Meldet diese Ereignisse:

| Ereignis | TikTok | Meta |
|---|---|---|
| Jede Seite | Pageview | PageView |
| Artikelseite | ViewContent | ViewContent |
| In den Warenkorb | AddToCart | AddToCart |
| Teamshop-Anfrage (/teamshops/ und /teamshop-anfrage/) | SubmitForm | Lead |
| Kontaktformular (/webshop/kontakt/) | Contact + SubmitForm | Contact + Lead |
| Bestellbestätigung | CompletePayment (mit Wert) | Purchase (mit Wert) |

## Google Tag Manager (ab Version 0.2.0)

- Einstellung **Google Tag Manager Container-ID** (z. B. `GTM-ABC1234`). Leer = Tag Manager aus (dann ändert sich nichts).
- Mit ID erscheint im Cookie-Banner der Eintrag „Google Analytics & Google Ads“. Der Container wird **erst nach Zustimmung** geladen.
- Google Consent Mode v2: Standard „abgelehnt“, nach Zustimmung „erteilt“ (analytics_storage, ad_storage, ad_user_data, ad_personalization).
- Das Plugin schreibt diese Ereignisse in den `dataLayer` (GA4-Format):

| dataLayer-Ereignis | Wann | Inhalt |
|---|---|---|
| `s39_consent_update` | Seitenaufruf und bei Änderung im Cookie-Banner | `s39_consent.google / meta / tiktok / solute` (true/false) |
| `view_item` | Artikelseite | ecommerce.items, value, currency |
| `add_to_cart` | In den Warenkorb | ecommerce.items, value, currency |
| `generate_lead` | Teamshop-Anfrage, Kontaktformular | `lead_type` |
| `purchase` | Bestellbestätigung | transaction_id, **value = Netto-Warenwert ohne Versand**, tax, shipping, items (Nettopreise), `s39_value_gross` (Brutto gesamt) |

- Im Tag Manager für jedes Tag die passende Einwilligung als Bedingung nutzen (dataLayer-Variable `s39_consent.google`, `.meta`, `.tiktok`).
- **Wichtig beim Umstieg:** Gleichzeitig mit dem Eintragen der Container-ID das Plugin „GoogleAnalytics“ deaktivieren (sonst doppelte Zählung). Meta/TikTok erst in den Tag Manager übernehmen, wenn dort getestet – dann hier die Pixel-ID leeren.

## billiger.de / solute (ab Version 0.2.0)

- Einstellung **billiger.de / solute Conversion-Tracking** = An. Eintrag „billiger.de (solute)“ im Cookie-Banner.
- Nach Zustimmung: Kommt der Besucher mit `soluteclid` in der URL, wird die Landingpage im Browser gespeichert (localStorage `soluteclid`, 30 Tage) und an `cmodul.solutenetwork.com/landing` gemeldet.
- Bestellbestätigung: Meldung an `cmodul.solutenetwork.com/conversion` mit `val` = Netto-Warenwert ohne Versand, `oid` = Auftrags-ID, `factor=1`.
- Danach solute (config@solute.de) Bescheid geben, damit sie das Tracking prüfen und freischalten.

## Einrichtung

1. Plugin ins Plugin-Set aufnehmen (Repo github.com/Sport39/s39-tracking, Branch main).
2. Im Plugin unter **Standard-Container** beide Datenquellen verknüpfen:
   - „S39 Tracking: Pixel-Basiscode“ → Ceres::Template.Style
   - „S39 Tracking: Ereignisse“ → Ceres::Script.AfterScriptsLoaded
3. Einstellungen prüfen (Pixel-IDs sind vorbelegt).
4. Set bereitstellen.

## Testen

Einstellung „Ereignisse in der Browser-Konsole ausgeben“ auf **Ja**, dann im Shop
im Cookie-Banner zustimmen und die Konsole öffnen (Einträge „[S39Tracking]“).
Zusätzlich: TikTok Events Manager → Pixel → „Ereignisse testen“, Meta Events Manager → „Events testen“.

## Datenschutz

In der Datenschutzerklärung je einen Absatz zu TikTok Pixel, Meta Pixel, Google Tag Manager/Analytics/Ads und billiger.de (solute) ergänzen.

## Teamshop-Anfrage: Versand

Das Formular auf /teamshops/ verschickt weiterhin das TeamShops-Plugin. Die Landingpage /teamshop-anfrage/ verschickt ihre Anfragen über den Mailversand des Kontaktformulars an webshop@sport39.de (Betreff „Teamshop-Anfrage: <Verein>“). Diese Anfragen werden nur einmal als Lead gemeldet, nicht zusätzlich als Kontakt.
