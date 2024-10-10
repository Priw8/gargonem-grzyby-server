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


## Licencja

[Unlicense](LICENSE)