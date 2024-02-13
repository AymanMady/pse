#include <iostream>
#include <time.h>
#include <stdlib.h>
#include<fstream>
#include<string>
#include<ctime>
using namespace std;
void creerUnficher(long n, string const nomFich) {

	ofstream flux(nomFich.c_str());

	if(flux) {
		long i;
		srand(time(NULL));

		for(i=1; i<n; i++) {
			int e = rand()%10000;
			flux <<e<<":";
		}
		flux <<rand()%10000<<endl;
	} else {
		cout << "ERREUR: Impossible d'ouvrir le fichier." << endl;
	}


}
long lireFichier(long tab[], char * nomF) {

	fstream fich;
	string  element;
	fich.open(nomF);
	long i=0;

	while(getline(fich,element,':')) {
		const char * els = element.c_str();
		tab[i++] = atoi(els);

	}
	fich.close();
	return i;
}

void triSelection(long tab[], long n){
	long i, j, tmp, index;
 
  for (i=0; i < (n-1); i++)
  {
    index = i;
   
    for (j=i + 1; j < n; j++)
    {
      if (tab[index] > tab[j])
        index = j;
    }
    if (index != i)
    {
      tmp = tab[i];
      tab[i] = tab[index];
      tab[index] = tmp;
    }
  }
}
void fusion(long t[], long debut1, long fin1, long fin2) {
    int i = debut1;
    int j = fin1 + 1;
    int k = 0;
    int temp[fin2 - debut1 + 1];
    while (i <= fin1 && j <= fin2) {
        if (t[i] < t[j]) {
            temp[k++] = t[i];
            i++;
        } else {
            temp[k++] = t[j];
            j++;
        }
    }
    while (i <= fin1) {
        temp[k++] = t[i++];
    }
    while (j <= fin2) {
        temp[k++] = t[j++];
    }
    for (k = 0; k <= fin2 - debut1; k++) {
        t[k + debut1] = temp[k];
    }
}
void triFusion(long t[], long debut, long fin) {
    if (fin - debut > 0) {
        int milieu = (debut + fin) / 2;
        triFusion(t, debut, milieu);      
        triFusion(t, milieu + 1, fin);   
        fusion(t, debut, milieu, fin);
    }
}

int main() {

	long taill=10000;
	while(taill<=70000){
	
	creerUnficher(taill,"tri3.txt");
	cout<<" debut tri d'un fichier \n";
	long  t[taill];
	long n = lireFichier(t,"tri3.txt");
	cout<<" nombre de valeur dans le fichier = "<<taill<<endl;
	clock_t start_time=clock();
     triFusion(t,0, n-1);
	clock_t end_time=clock() ;
   double tamp_fision=(double(end_time-start_time)/CLOCKS_PER_SEC)*1000;
	cout<<" le temps de tri fision: "<<tamp_fision <<" en milisecond";
	clock_t start_time_s=clock();
    triSelection(t,n);
clock_t end_time_s=clock() ;
  double tamp_selection=(double(end_time_s-start_time_s)/CLOCKS_PER_SEC)*1000;
  cout<<endl;
cout<<" le temps de tri selection: "<<tamp_selection <<" en milisecond";
cout<<"  \n \n";
taill+=10000;
}
	
return 0;
}


