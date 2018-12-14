# OGÓLNE INFORMACJE
1. Do stylowania szablonów używamy bootstrapa w wersji czwartej.
2. Wszystkie formularze walidujemy po stronie serwera. Walidację HTML wyłączamy w każdym formularzu.
3. Jak piszę o adminie/adminach to mam na myśli rolę admina i super admina. Jeżeli występuje rozróżnienie to wtedy jest to wyraźnie napisane.
4. Utwórz katalog dumps/ w katalogu projektowym i wrzucaj tam dumpy swojej bazy danych (zwłaszcza przed pull requestami). Wtedy podczas testowania Twojej aplikacji będę mógł wrzucić najaktualniejszy zrzut bazy i będę miał taki sam stan zawartości na stronie jak Ty co pomoże w weryfikacji kodu i działania aplikacji.
5. Przedstawiłem ogólny zarys jak ma wyglądać aplikacja, bez wchodzenia w szczegóły, bo nie o to chodzi. Musisz kierować się logiką i sam podejmować decyzje co jeszcze powinno znaleźć się na wybranej stronie, aby wszystko miało ręce i nogi.

# STRONY
* Lista artykułów
* Strona pojedynczego artykułu
* Tworzenie artykułu
* Edycja artykułu
* Strona rejestracji
* Strona logowania
* Konto użytkownika
* Strona do kontaktu
* Panel admina i jego podstrony


# UŻYTKOWNICY W APLIKACJI
(to jest hierarchia, więc każda ważniejsza rola ma również wszystkie pozwolenia mniej ważnych ról):

* Anonimowy
  * może wyświetlać artykuły i komentować je
* Zwykły Użytkownik
  * może dodawać/edytować swoje artykuły (nie może publikować)
* Admin
  * może zarządzać artykułami i je publikować
  * może zarządzać użytkownikami
* Super Admin
  * może zarządzać adminami


# SZCZEGÓŁOWY OPIS STRON
## Ad. Lista artykułów
- Lista w postaci tabeli. Przemyśl, które pola encji powinny zostać zawarte w tabeli, a które dopiero w formularzu.
- Dodatkowa kolumna z przyciskami (Pokaż, Edytuj, Usuń, Opublikuj)
- Przyciski "Opublikuj" i "Usuń" widoczne tylko dla Adminów
- Przycisk "Opublikuj" powinien kierować do akcji bez widoku i ta akcja powinna przekierowywać z powrotem na listę z dodaną wiadomością flash czy publikacją się udała czy nie.
- Przycisk "Edytuj" widoczny tylko dla użytkowników. Przy zwykłych użytkownikach tylko przy artykułach, które zostały utworzone przez nich, zaś dla adminów przy wszystkich.
- Nad tabelą z lewej strony dwa inputy do filtrowania listy, gdzie użytkownik będzie mógł wpisać daty OD - DO i na tej podstawie wyfiltrujesz wyświetlane artykuły
- Nad tabelą z prawej strony selekt z sortowaniem: "Nazwa - rosnąco", "Nazwa - malejąco", "Data utworzenia - rosnąco", "Data utworzenia - malejąco"
- Paginacja listy. Jednorazowo wyświetlane jest 10 artykułów.
- Pod tabelą, ale nad paginacją możliwość wybrania ilości wyświetlanych artykułów na liście. Niech będzie 10|15|20|25|30

## Ad. Strona pojedynczego artykułu
- Dodajemy zdjęcie do artykułu.
- Pod artykułem powinny znaleźć się komentarze. Komentarze mogą być dodawane przez kogokolwiek. Pod komentarzami powinien znaleźć się formularz dodawania nowego komentarza z polami: "Imię", Email", 'Treść komentarza". Jeżeli komentarz dodaje zalogowany użytkownik to w dwa pierwsze pola wstawiamy wartości odpowiednich pól z encji User i blokujemy te pola, aby nie można było ich edytować. Oczywiście komentarz powinien być w relacji do artykułu.
- Do pola formularza reprezentujego treść komentarza dodasz samodzielnie napisany walidator. Powinien on walidować czy komentarz nie zawiera wybranych słów (dowolnie jakich, sam wymyśl kilkanaście. Chodzi o logikę.)
- Możliwość pobrania artykuł na dysk w formie PDF. Czyli powinien znaleźć się przycisk "Pobierz artykuł". Do utworzenia PDF będzisz potrzebował zewnętrznej paczki.

## Ad. Tworzenie artykułu & Ad. Edycja artykułu
- Trzeba dodać pole do encji Article, które będzie przechowywać informacje o zdjęciu, zaś w formularzu powinno znaleźć się pole do dodania zdjęcia, aby użytkownik mógł wybrać je z dysku i przypisać do tego artykułu.
- Artykuły powinny mieć tagi (jeden artykuł może mieć wiele tagów). W encji będzie to pole typu array, zaś w formularzu będziesz wyświetlał tagi jako string, gdzie tagi są oddzielone przecinkami (użyj pola typu TextareaType).
- pole isPublished powinno być dodane do formularza tylko dla adminów. Musisz zastanowić się jak dodać nowe pole do formularza w zależności od okoliczności. Żebyś nie zrobił tak, że to pole będzie zawsze, a w szablonie sobie je ukryjesz - tak jest niepoprawnie.
- Pole "Autor" nie powinno wyświetlać się dla użytkowników z rolą zwykłego użytkownika. W tym przypadku autorem jest on sam, więc musisz sam to ustawić w kodzie. W przypadku adminów to pole będzie wyświetlane. Admin będzie miał możliwość ustawienia autora artykułu. Tak więc, kiedy admin tworzy/edytuje artykuł to powinno być pole typu EntityType, aby admin mógł wybrać autora. Dodatkowo zrób tak, że label będzie w takiej formie: "Imię Nazwisko (email) <tutaj_rola>", gdzie <tutaj_rola> będzie na czerwono i pokazana tylko wtedy, gdy rola to Admin lub wyżej. Chcemy rozróżniać czy użytkownik, do którego przypisują artykuł jest adminem.

## Ad. Strona rejestracji
- Captcha w formularzy rejestracji.
- Checkbox "Akceptuję regulamin strony". Musi być zaznaczony, aby przejść walidację.
- Formularz rejestracji powinien być zbindowany do encji User, ale nie dodajemy captych'y i zgody do encji użytkownika.
- Po poprawnym zarejestrowaniu od razu zalogowanie.
- Do strony rejestracji nie powinien mieć dostępu już zalogowany użytkownik.


## Ad. Strona logowania
- Formularz logowania zrób w tradycyjnym HTML, bez wykorzystania Form Component.
- Funkcjonalność **Zapamiętaj mnie**. Ustaw czas na 1 tydzień.
- Do strony logowania nie powinien mieć dostępu już zalogowany użytkownik.

## Ad. Konto użytkownika
- Użytkownik powinien mieć możliwość edycji swoich danych, jak również zmiany hasła.
- Pole aktualnego hasła walidujesz tylko wtedy, gdy wypełniono pola nowego hasła.
- Możliwość usunięcia konta (Przycisk "Usuń konto"). Musi być potwierdzenie. Skorzystaj z modala od bootstrapa.
- Do encji użytkownika dodajemy pole "agreeNewArticleNotification", a w formularzu edycji konta dodajemy checkboxa "Chcę otrzymywać powiadomienia o nowych artykułach". Przypomnienia wysyłamy **po publikacji artykułu**, nie po jego utworzeniu. W związku z tym musisz znaleźć w aplikacji miejsca, gdzie następuje publikacja artykułu i zaimplementować tam wysyłkę maili do użytkowników, którzy zaznaczyli ten checkbox na swoich kontach. Szablon maila prosty - nie chodzi nam o wygląd, ale o działający mechanizm wysyłki przypomnień.


## Ad. Panel admina i jego podstrony
- Panel dostępny tylko dla adminów. Ma być strona z listą użytkowników (usuwanie z potwierdzeniem w kolumnie z opcjami) i edycji. Nie robimy tworzenia nowych użytkowników. Tutaj mechanizm powinien być taki, że Admin może edytować tylko zwykłych użytkowników, zaś Super Admin zarówno zwykłych użytkowników jak i adminów (nie super adminów). Dodatkowo tylko Super Admin może widzieć i zmieniać role użytkownika.
- W szablonie edycji użytkownika powinna się tabela listująca aktywności użytkownika.
- Dla adminów dodajemy funckjonalność podszywania się pod użytkowników, tzw. impersonizacja.

## Ad. Strona do kontaktu
- Formularz do kontaktu. Pola: "Imię", "Email", "Tytuł", "Treść", "Wyślij do mnie kopię wiadomości" (checkbox). W przypadku tego formularza, pola "Imię" i "Email" ustawiamy domyślnie na wartości aktualnie zalogowanego użytkownika tylko nie blokujemy ich - są edytowalne. Przygotuj prosty szablon maila, który wykorzystasz do wysłania maila zawierającego dane i treść wpisaną w formularzu. Mail powinien się wysyłać po zatwierdzeniu formularza. Domyślnie na adres email ustawiony w konfiguracji. Jeżeli zaznaczono checkbox "Wyślij do mnie kopię wiadomości" wysyłasz również tego samego maila do osoby, która wypełniła formularz. Dodatkowo w mailu powinien być zawarty adres IP komputera, z którego wysłano formularz.


# INNE FEATURE'Y:
* Logowanie akcji użytkownika (osobna encja UserLogs). Logujemy następujące akcje użytkownika: poprawne logowanie, niepoprawne logowanie.
* Zapis na newsletter. Przygotuj stopkę, oczywiście nie styluj jej jakoś super, po prostu ma wyglądać po ludzku, a nie straszyć wyglądem. W niej dodaj formularz z inputem do zapisu na newsletter, gdzie ktokolwiek może wpisać email. Zapisuj te adresy e-mail w encji Newsletter.

# KOMENDY
- Komenda usuwająca użytkowników, którzy nie wykonali akcji od 3 miesięcy. Napiszesz Symfony'iową komendę, którą potem będzie można podpiąć do Cron'a i która będzie usuwała użytkowników, którzy nie wykonali żadnej akcji od 3 miesięcy.
- Komenda do wysyłki newsletterów. Przygotuj prosty szablon maila, który będzie newsletterem (treść newslettera bez znaczenia). Komenda będzie podpięta do Cron'a i ma funkcjonować tak, że w wybrany dzień tygodnia (Jaki? Ustaw w konfiguracji, aby można było zmieniać ten dzień bez zmian w kodzie komendy) wysyła maila z newsletterem na adresy e-mail, które subskrybowały na newsletter.
