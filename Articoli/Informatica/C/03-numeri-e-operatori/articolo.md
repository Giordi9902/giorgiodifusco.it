Nei linguaggi moderni siamo stati viziati. Se sei abituato a JavaScript, scrivi `let x = 10`, poi magari decidi che `x` deve diventare una stringa, e l'interprete annuisce e fa il lavoro sporco per te. In C, questa flessibilità semplicemente non esiste. Se vuoi dello spazio in memoria, devi dire al compilatore esattamente di quanti byte hai bisogno e cosa ci metterai dentro.

Sembra una scocciatura, ma è proprio questa rigidità che ti permette di far girare il tuo codice letteralmente ovunque: su un server da 128 core così come su un microcontrollore Arduino con 2KB di RAM.

## La bugia dell'int e la salvezza di stdint.h

Se hai già sbirciato del codice C, avrai sicuramente visto dichiarazioni come `int contatore = 0;`. Sembra innocuo, ma c'è un problema subdolo: lo standard del C non definisce esattamente quanto sia grande un `int`. Ti dice solo che deve essere almeno 2 byte. Su un vecchio sistema a 16 bit, un `int` occupa 2 byte. Sul tuo laptop moderno a 64 bit, ne occupa 4.

Se stai scrivendo un protocollo di rete o stai leggendo i registri di un sensore, non puoi affidarti al caso. Hai bisogno di certezze assolute.

Ecco perché i veri sviluppatori C si affidano a un header introdotto con lo standard C99: `<stdint.h>`. Importando questa libreria, smetti di usare definizioni vaghe e inizi a parlare la lingua dell'hardware:

- `uint8_t`: Intero senza segno, esattamente 1 byte (da 0 a 255). Perfetto per manipolare singoli caratteri o dati raw.
- `int32_t`: Intero con segno, esattamente 4 byte.
- `uint64_t`: Intero senza segno, esattamente 8 byte. Ideale per timestamp o contatori astronomici.

Usare questi tipi ti costringe a pensare ai limiti. Cosa succede se aggiungi 1 a un `uint8_t` che vale 255? Non si espande magicamente. Fa overflow. Torna a zero, esattamente come il contachilometri di una vecchia Fiat Panda che arriva a 999.999 e ricomincia da capo. Attenzione però: questo "giro completo" è garantito dallo standard solo per i tipi senza segno. Con i tipi con segno (`int8_t`, `int32_t`...) l'overflow è *comportamento indefinito*: il compilatore è libero di assumere che non accada mai, e il risultato può essere qualsiasi cosa. In crittografia o nei sistemi embedded, non gestire un overflow significa creare una vulnerabilità critica.

## Gli operatori bitwise

Il controllo di flusso in C (i classici `if`, `while`, `for`) funziona esattamente come in Java o JavaScript. Non c'è molto di nuovo da imparare lì, se non che sotto il cofano il compilatore traduce quei costrutti in semplici istruzioni di salto (`JMP`, `JE`, `JNE`...) in Assembly.

La vera goduria del C sono gli operatori bitwise. Ti permettono di scavalcare il concetto di "numero" e andare a manipolare direttamente i singoli bit (gli 1 e gli 0) che compongono quel numero nella RAM.

I fantastici quattro sono:

- AND (`&`): Restituisce 1 solo se entrambi i bit sono 1. Ottimo per "mascherare" e spegnere bit specifici.
- OR (`|`): Restituisce 1 se almeno uno dei bit è 1. Usato per accendere bit specifici.
- XOR (`^`): Restituisce 1 solo se i bit sono diversi. È la base della crittografia leggera.
- Shift (`<<` e `>>`): Sposta letteralmente tutti i bit a sinistra o a destra. Uno shift a sinistra (`<< 1`) equivale a moltiplicare per 2 in modo brutalmente veloce, a livello hardware.

## Costruiamo un convertitore da decimale a binario

Per capire davvero i bitwise, non c'è niente di meglio che usarli per guardare dentro la memoria. Costruiremo un piccolo programma che prende un numero intero e usa lo shift e una maschera bitwise per stampare a schermo la sua esatta rappresentazione binaria.

Apri il tuo editor, crea un file `bit.c` e inserisci questo codice:

```c
#include <stdio.h>
#include <stdint.h>

int main(void) {
    // Usiamo un intero senza segno a 16 bit (2 byte)
    uint16_t numero = 42;

    printf("Il numero decimale è: %u\n", numero);
    printf("La sua rappresentazione binaria è: ");

    // Un uint16_t ha 16 bit. Partiamo dal bit più significativo (il 15esimo)
    // e scendiamo fino al bit 0.
    for (int i = 15; i >= 0; i--) {
        // 1. Spostiamo il bit che ci interessa nella prima posizione a destra (posizione 0)
        uint16_t bit_spostato = numero >> i;

        // 2. Usiamo un AND bitwise con 1 (che in binario è ...00000001)
        // Questo azzera tutti gli altri bit e ci lascia solo il valore dell'ultimo bit (0 o 1)
        uint16_t bit_reale = bit_spostato & 1;

        // Stampiamo il singolo bit
        printf("%u", bit_reale);

        // Aggiungiamo uno spazio ogni 4 bit per renderlo leggibile
        if (i % 4 == 0) {
            printf(" ");
        }
    }

    printf("\n");
    return 0;
}
```

Compilalo con

```bash
gcc bit.c -Wall -Wextra -o bit
```

ed eseguilo con `./bit`. Vedrai il numero 42 trasformarsi in un nudo e crudo `0000 0000 0010 1010`.

Senza usare conversioni magiche in stringa offerte da librerie di alto livello, abbiamo estratto l'informazione direttamente dal silicio, bit dopo bit. Questo è il pattern esatto che useresti per leggere lo stato dei pin digitali su un microcontrollore o per impacchettare pixel in un formato immagine raw.

Nel prossimo articolo alzeremo la posta in gioco. Prenderemo questi tipi di dati primitivi e vedremo come il C ci permette di navigarci attraverso usando gli indirizzi di memoria. Preparati ad affrontare i puntatori e l'aritmetica dei puntatori.
