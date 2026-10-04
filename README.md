# gargonem-grzyby-server

Kod prostego serwera do dodatku Grzybotimer. Więcej informacji znajdziesz [na stronie dodatku](https://gargonem.margoworld.pl/dodatki/grzybotimer).

## Jak używać?

Chciałem żeby było jak najprostsze w konfiguracji, więc serwer napisany jest w PHP - można po prostu wrzucić pliki i po skonfigurowaniu powinno śmigać.

Tak więc najpierw trzeba wrzucić te pliki na serwer - najlepiej pobrać z Githuba zip z repo i wypakować go gdzieś na serwerze (można też zrobić `git clone`, ale wtedy warto się upewnić że web server nie będzie miał dostępu do folderu `.git`). Następnym krokiem jest utworzenie folderu `data`, a w nim pliku `timestamps.json` o zawartości `{}`. Serwer będzie zapisywał w nim timery grzybów.

Na koniec otwieramy plik `config.php` i zmieniamy wartości zmiennych zgodnie z komentarzami nad nimi. Potem po ustawieniu adresu serwera w konfiguracji dodatku w Gargonem, wszystko powinno działać.

## Troubleshooting

Jeżeli coś nie trybi, warto sprawdzić następujące rzeczy:

- czy użytkownik procesu PHP ma dostęp do zapisywania pliku `data/timestamps.json`
- czy serwer nie wysyła dwukrotnie nagłówków CORS (jak konfiguracja samego serwera zapewnia odpowiednie nagłówki, należy usunąć CORS z `common-headers.php`)
- czy zapytania z gry idą na dobry adres (devtoolsy i zakładka `Network` do sprawdzenia)
- logi w konsoli przeglądarki (w szczególności te zaczynające się od `[Gargonem::Grzyby]`)

## Zmiany w wersji 1.1

Nowa wersja zwraca dodatkowe informacje dzięki którym dodatek lepiej działa:

- w momencie zapisywania timera od razu zwraca zaktualizowaną listę timerów, żeby minutnik nie czekał z odświeżeniem danych
- zmienił się format zwracania danych: wcześniej dane grzybów były w JSONie w formie `{"Nazwa-lvl": timestamp}`, a teraz jest to `{"Nazwa-lvl": { "ts": timestamp, location: "mapa (x,y)" }}`. Dzięki temu dodatek pokazuje w dymku na minutniku gdzie aktualnie znajduje się otwarty grzyb.

Ogółem dodatek jest kompatybilny wstecznie ze starymi wersjami serwera więc nie trzeba aktualizować, no ale bez tego nowe ficzery nie będą działały bo z fusów nie wywróży brakujących danych.

Jeżeli ktoś miał własną implementację serwera (wiem że co najmniej 1 osoba zrobiła xd) i chce dodać analogiczne zmiany:

- endpoint `read.php` zwraca teraz format wspomniany wcześniej, czyli `{"Nazwa-lvl": { "ts": timestamp, location: "mapa (x,y)" }, ...}` zamiast samego timestampa per grzyb.
- endpoint `save.php` zwracał wcześniej coś takiego: 
  ```ts
  {
    ok: number;
    msg?: string;
  }
  ```
  Teraz dochodzi tutaj pole `timers`, które zawiera listę timerów (1:1 odpowiedź z `read.php`). Więc przykładowa odpowiedź po udanym zapisaniu:
  ```json
  {
    "ok": 1,
    "timers": { "Nazwa-lvl": { "ts": 1791105706, "location": "Głębokie Skałki (12,15)" } }
  }
  ```


## Licencja

[Unlicense](LICENSE)