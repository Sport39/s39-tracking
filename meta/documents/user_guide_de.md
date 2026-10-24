# S39Tracking

TikTok-Pixel und Meta-Pixel für sport39.de – nur nach Einwilligung im Cookie-Banner.

## Was das Plugin macht

- Meldet „TikTok Pixel“ und „Meta Pixel (Facebook/Instagram)“ als eigene Einträge im Cookie-Banner an (Gruppe einstellbar, Standard: Tracking).
- Lädt die Pixel nur, wenn der Besucher zugestimmt hat.
- Meldet diese Ereignisse:

| Ereignis | TikTok | Meta |
|---|---|---|
| Jede Seite | Pageview | PageView |
| Artikelseite | ViewContent | ViewContent |
| In den Warenkorb | AddToCart | AddToCart |
| Teamshop-Anfrage (/teamshops/) | SubmitForm | Lead |
| Kontaktformular (/webshop/kontakt/) | Contact + SubmitForm | Contact + Lead |
| Bestellbestätigung | CompletePayment (mit Wert) | Purchase (mit Wert) |

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

In der Datenschutzerklärung je einen Absatz zu TikTok Pixel und Meta Pixel ergänzen.
