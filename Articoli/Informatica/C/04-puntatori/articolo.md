Chiedi a uno studente di informatica qual è stato il momento in cui ha pensato di mollare tutto e aprire un chiringuito sulla spiaggia, e nove su dieci ti risponderanno con una sola parola: i puntatori.

Circondati da un'aura di terrore e misticismo, i puntatori sono lo spauracchio definitivo di chi inizia a programmare in C. Eppure, ti svelo un segreto: non c'è nessuna magia nera. Una volta capito il trucco, ti accorgerai che sono uno dei concetti più logici e lineari dell'intera informatica. E anche uno dei più potenti.

Nell'articolo precedente abbiamo visto come i tipi di dato occupano un numero preciso di byte. Oggi scopriamo dove vivono quei byte e come raggiungerli. Mettiti comodo, perché stiamo per scendere a livello hardware.

## Indirizzi e case: l'unica analogia che ti serve

Immagina la memoria RAM del tuo computer come una strada lunghissima, fiancheggiata da milioni di casette tutte uguali. Ogni casetta contiene esattamente un byte di dati e, soprattutto, ha un numero civico univoco. Quel numero civico è l'**indirizzo di memoria**.

Quando dichiari una variabile, per esempio `int punteggio = 100;`, stai affittando un gruppo di casette contigue (4, su praticamente ogni piattaforma moderna) e ci stai mettendo dentro un valore.

Un **puntatore** non è altro che una variabile che, invece di contenere un numero o una lettera, contiene un numero civico. Tutto qui. È un foglietto di carta con scritto sopra: "il punteggio lo trovi alla casetta numero `0x7ffe5c10`".

Anche il puntatore, ovviamente, è una variabile: abita a sua volta in qualche casetta (8 byte su un sistema a 64 bit) e ha un suo indirizzo.

## I due superpoteri: `&` e `*`

Per maneggiare i puntatori ti servono due operatori. Stampali a fuoco nella mente:

- **`&` (indirizzo di)**: è la domanda "dove abiti?". Messo davanti a una variabile, non ti restituisce il suo valore, ma il suo numero civico nella RAM.
- **`*` (dereferenziazione)**: è l'azione "entra in quella casa e guarda cosa c'è dentro". Messo davanti a un puntatore, dice alla CPU di andare a quell'indirizzo e leggere, o modificare, il dato vero e proprio.

Vediamoli al lavoro:

```c
#include <stdio.h>

int main(void) {
    int punteggio = 100;

    // Dichiariamo un puntatore a int e ci salviamo dentro l'indirizzo di 'punteggio'
    int *ptr = &punteggio;

    printf("Valore di punteggio:    %d\n", punteggio);
    printf("Indirizzo (&punteggio): %p\n", (void *)&punteggio);
    printf("Contenuto di ptr:       %p\n", (void *)ptr);   // lo stesso indirizzo!
    printf("Valore puntato (*ptr):  %d\n", *ptr);          // 100

    // Modifichiamo il dato passando dal puntatore
    *ptr = 250;
    printf("Ora punteggio vale:     %d\n", punteggio);     // 250

    return 0;
}
```

Occhio a una trappola sintattica che confonde tutti all'inizio: l'asterisco ha due significati diversi a seconda di dove si trova. Nella **dichiarazione** (`int *ptr`) significa "ptr è un puntatore a int". In un'**espressione** (`*ptr = 250`) significa "vai all'indirizzo contenuto in ptr". Stesso simbolo, due mestieri.

E un avvertimento: un puntatore dichiarato ma non inizializzato contiene un indirizzo casuale. Dereferenziarlo significa entrare in una casa a caso della strada, con risultati che vanno dal crash immediato al bug fantasma che compare una volta su mille. Se non sai ancora dove deve puntare, inizializzalo a `NULL`, l'indirizzo "nessuna casa", e controllalo prima di usarlo.

## Il dramma del passaggio per valore (e come i puntatori ci salvano)

Perché complicarsi la vita con gli indirizzi? Perché in C gli argomenti delle funzioni vengono **sempre** passati per valore.

Immagina di voler scrivere una funzione che scambia i valori di due variabili, la classica `swap(a, b)`. Quando la chiami, il C prende il valore di `a` e di `b`, ne fa una fotocopia e consegna le fotocopie alla funzione. La funzione scambia le fotocopie, termina, le fotocopie vengono distrutte... e le tue variabili originali sono rimaste identiche. Hai appena sprecato cicli di CPU per nulla.

La soluzione è simulare il **passaggio per riferimento** tramite i puntatori. Non passare la fotocopia del dato: passa il numero civico. La funzione riceve comunque una copia, ma è la copia di un indirizzo, e con un indirizzo si può andare a modificare l'originale.

```c
#include <stdio.h>

// La funzione si aspetta due indirizzi di memoria, non due numeri!
void swap(int *a, int *b) {
    // Entriamo nella casa 'a' e mettiamo il valore in una cassaforte temporanea
    int temp = *a;

    // Entriamo nella casa 'b', prendiamo il valore e lo mettiamo nella casa 'a'
    *a = *b;

    // Riprendiamo il valore dalla cassaforte e lo mettiamo nella casa 'b'
    *b = temp;
}

int main(void) {
    int x = 10;
    int y = 99;

    // Con & passiamo gli indirizzi esatti di x e y
    swap(&x, &y);

    printf("Risultato: x = %d, y = %d\n", x, y); // x = 99, y = 10
    return 0;
}
```

Se vieni da Java o Python, questo meccanismo lo usi già senza saperlo: quando passi un oggetto a un metodo, in realtà stai passando (per valore) un riferimento a quell'oggetto. Il C semplicemente non te lo nasconde.

## L'aritmetica dei puntatori

Fin qui tutto logico. Ma il C fa un passo in più e ti permette di fare operazioni matematiche sugli indirizzi. Questa è l'**aritmetica dei puntatori**.

Se un puntatore `ptr` contiene l'indirizzo 1000, cosa succede se scrivo `ptr + 1`? La risposta istintiva è 1001. Sbagliato.

Il compilatore sa a quale tipo di dato punta il tuo puntatore, e ragiona in **elementi**, non in byte. Se `ptr` è un `int *` e un `int` occupa 4 byte, `ptr + 1` salta avanti di esattamente 4 byte, fino all'indirizzo 1004. Se fosse un `uint64_t *` salterebbe a 1008; se fosse un `char *`, che occupa 1 byte, arriveresti davvero a 1001. In formula: `ptr + n` avanza di `n * sizeof(*ptr)` byte.

Lo stesso vale per la sottrazione: la differenza tra due puntatori dello stesso tipo non è il numero di byte che li separa, ma il numero di **elementi**.

Questa non è una stranezza da nerd: è il meccanismo con cui si scorrono gli array e si gestiscono i buffer di dati grezzi, come i ring buffer che si usano sui microcontrollori o nel networking. In C, infatti, il nome di un array usato in un'espressione "decade" in un puntatore al suo primo elemento, e la scrittura `v[i]` è per definizione equivalente a `*(v + i)`.

C'è però una regola d'oro: l'aritmetica dei puntatori ha senso solo **all'interno dello stesso blocco di memoria** (lo stesso array o la stessa stringa), più al massimo una posizione oltre la fine. Uscire da quei confini è comportamento indefinito, e ci torneremo molto presto.

## Scriviamo la nostra libreria: reinventiamo `strlen()`

Per vedere l'aritmetica dei puntatori in azione, reimplementiamo da zero una delle funzioni più usate della libreria standard: `strlen()`, quella che conta i caratteri di una stringa.

In C una stringa è solo una sequenza contigua di caratteri (`char`, 1 byte ciascuno) terminata da un carattere speciale, il terminatore nullo `'\0'`, il cui valore è proprio zero.

Invece di usare indici e contatori, usiamo la matematica della RAM:

```c
#include <stdio.h>
#include <stddef.h>

// Riceviamo un puntatore al primo carattere della stringa
size_t my_strlen(const char *str) {
    // Ci segniamo l'indirizzo di partenza
    const char *inizio = str;

    // Avanziamo di casa in casa (un char = 1 byte alla volta)
    // finché non troviamo il terminatore '\0', che vale 0, cioè "falso"
    while (*str) {
        str++; // Aritmetica dei puntatori!
    }

    // Indirizzo finale meno indirizzo iniziale: il numero di caratteri tra i due
    return (size_t)(str - inizio);
}

int main(void) {
    // 'messaggio' punta alla 'H' di una stringa letterale (che non va modificata: da qui il const)
    const char *messaggio = "Hello, Kernel!";

    printf("La stringa '%s' è lunga %zu caratteri.\n", messaggio, my_strlen(messaggio));

    return 0;
}
```

Compilalo con

```bash
gcc strlen.c -Wall -Wextra -o strlen
```

ed eseguilo con `./strlen`: otterrai `La stringa 'Hello, Kernel!' è lunga 14 caratteri.`

Guarda l'eleganza della riga `return str - inizio;`. Stiamo letteralmente sottraendo un indirizzo di memoria da un altro per scoprire quanti caratteri li separano. Nessun contatore, solo navigazione nella RAM. Il risultato di questa sottrazione ha tipo `ptrdiff_t` (un intero con segno); qui sappiamo che non può essere negativo, quindi lo convertiamo tranquillamente in `size_t`, il tipo che la vera `strlen()` restituisce.

Se hai capito questo frammento di codice, hai ufficialmente sconfitto il boss finale e sei pronto per il livello successivo.

Nel prossimo articolo parleremo di array, e ormai conosci già il loro segreto più intimo. Soprattutto, vedremo come il C ti lascia spararti sui piedi quando ignori i limiti della memoria: parleremo del leggendario buffer overflow e di come manipolare le stringhe senza far crashare tutto.
