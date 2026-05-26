/**
 * Lista de ciudades/municipios reales de Colombia para validación.
 * Compara ignorando mayúsculas, minúsculas, tildes y espacios extra.
 */
window.CO_CITIES = [
  // Capitales departamentales y principales
  'Bogota','Bogota DC','Bogota D.C.','Medellin','Cali','Barranquilla','Cartagena','Cucuta',
  'Bucaramanga','Pereira','Santa Marta','Ibague','Pasto','Manizales','Neiva','Villavicencio',
  'Armenia','Valledupar','Monteria','Sincelejo','Popayan','Tunja','Florencia','Riohacha',
  'Yopal','Quibdo','Mocoa','Leticia','Arauca','Inirida','Mitu','Puerto Carreno','San Andres',
  // Área metropolitana de Medellín
  'Bello','Itagui','Envigado','Sabaneta','La Estrella','Caldas','Copacabana','Girardota','Barbosa',
  // Área metropolitana de Bogotá / Sabana
  'Soacha','Chia','Cota','Cajica','Tabio','Tenjo','Mosquera','Madrid','Funza','Facatativa',
  'Zipaquirá','Zipaquira','Sopo','La Calera','Tocancipa','Gachancipa','Sibate','Subachoque','El Rosal',
  'Bojaca','Fusagasuga','Silvania','Pacho','Ubate','Choconta','Villapinzon','Sesquile','Guasca',
  'Guaduas','Villeta','La Vega','Anolaima','Cachipay','Apulo','La Mesa','Tena','Cachipai',
  // Atlántico / Caribe
  'Soledad','Malambo','Galapa','Puerto Colombia','Sabanalarga','Sabanagrande','Baranoa',
  'Turbaco','Arjona','Magangue','El Carmen de Bolivar','Mompox','Mompos','San Juan Nepomuceno',
  'Cienaga','Fundacion','El Banco','Pivijay','Aracataca','Plato','Sitionuevo','Salamina',
  'Maicao','Uribia','Manaure','San Juan del Cesar','Fonseca','Barrancas','Hatonuevo',
  'Lorica','Cerete','Sahagun','Planeta Rica','Monitos','Tierralta','Puerto Libertador',
  'Corozal','San Marcos','San Onofre','Tolu','Coveñas','Covenas','Sampues','Sampues',
  // Valle del Cauca
  'Palmira','Buenaventura','Tulua','Cartago','Buga','Yumbo','Jamundi','Candelaria',
  'Florida','Pradera','Roldanillo','Sevilla','Caicedonia','Zarzal','La Union','Andalucia',
  'Bugalagrande','Dagua','Restrepo','Calima','El Cerrito','Ginebra','Guacari',
  // Cauca / Nariño
  'Puerto Tejada','Santander de Quilichao','Caloto','Miranda','Corinto','Patia','Bolivar',
  'Tumaco','Tuquerres','Ipiales','La Union','Samaniego','Sandona','Tangua','Buesaco',
  // Antioquia
  'Apartado','Turbo','Chigorodo','Carepa','Mutata','Necocli','San Pedro de Uraba','Arboletes',
  'Caucasia','El Bagre','Zaragoza','Taraza','Caceres','Nechi','Segovia','Remedios',
  'Rionegro','Marinilla','La Ceja','El Carmen de Viboral','Guarne','El Retiro','La Union',
  'Sonson','Abejorral','Andes','Jardin','Jerico','Santa Fe de Antioquia','Sopetran','San Jeronimo',
  'Yarumal','Santa Rosa de Osos','Donmatias','Entrerrios','San Pedro de los Milagros',
  // Santander
  'Floridablanca','Giron','Piedecuesta','Lebrija','Los Santos','Barrancabermeja','San Gil',
  'Socorro','Barbosa','Velez','Charala','Puente Nacional','Malaga','Cimitarra','Sabana de Torres',
  // Norte de Santander
  'Ocana','Pamplona','Villa del Rosario','Los Patios','El Zulia','Tibu','Sardinata','Chinacota',
  // Boyacá
  'Sogamoso','Duitama','Chiquinquira','Paipa','Villa de Leyva','Villa de Leiva','Moniquira',
  'Garagoa','Samaca','Nobsa','Tibasosa','Soata','Saboya','Puerto Boyaca','Otanche',
  // Tolima
  'Espinal','Honda','Melgar','Mariquita','Chaparral','Lerida','Libano','Purificacion',
  'Flandes','Guamo','Saldana','Ortega','Coyaima','Natagaima','Ambalema','Falan',
  // Huila
  'Pitalito','Garzon','La Plata','Campoalegre','Rivera','Aipe','Yaguara','Tello',
  'San Agustin','Isnos','Acevedo','Suaza','Gigante','Hobo','Iquira','Palermo',
  // Caldas / Risaralda / Quindío
  'La Dorada','Riosucio','Anserma','Aguadas','Pacora','Salamina','Manzanares','Pensilvania',
  'Dosquebradas','Santa Rosa de Cabal','La Virginia','Belen de Umbria','Quinchia','Marsella',
  'Calarca','La Tebaida','Montenegro','Quimbay','Quimbaya','Salento','Filandia','Circasia','Pijao',
  // Meta / Llanos
  'Acacias','Granada','Puerto Lopez','Cumaral','San Martin','Restrepo','Villanueva',
  'Tame','Saravena','Arauquita','Fortul','Cravo Norte','Puerto Rondon',
  'Aguazul','Tauramena','Monterrey','Villanueva','Pore','Trinidad','Hato Corozal','Paz de Ariporo',
  // Magdalena Medio / Cesar
  'Aguachica','La Jagua de Ibirico','Bosconia','Codazzi','Curumani','Pailitas','Pelaya','Tamalameque',
  'Astrea','Chimichagua','Chiriguana','El Copey','El Paso','Gamarra','Gonzalez','La Gloria',
  // Putumayo / Caquetá / Amazonas
  'Puerto Asis','Orito','Valle del Guamuez','La Hormiga','San Miguel','Sibundoy','Colon',
  'San Vicente del Caguan','Belen de los Andaquies','Puerto Rico','Cartagena del Chaira','Curillo',
  'San Jose del Guaviare','El Retorno','Calamar','Miraflores',
  // Chocó
  'Istmina','Tado','Condoto','Acandí','Bahia Solano','Nuqui','Riosucio','Bojaya','Lloro',
  // Otros
  'Mompós','Mompós','Aratoca','El Cerrito','Gomez Plata','Yolombo','Ricaurte','Carmen de Apicala'
];

(function () {
  function normalize(s) {
    return (s || '')
      .toString()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-zA-Z\s\.]/g, '')
      .replace(/\s+/g, ' ')
      .trim()
      .toLowerCase();
  }

  // Set normalizado para búsqueda O(1)
  const set = new Set(window.CO_CITIES.map(normalize));

  // Permite también escribir solo "Bogota" en lugar de "Bogota D.C." y viceversa
  window.isColombianCity = function (input) {
    if (!input) return false;
    const n = normalize(input);
    if (!n) return false;
    if (set.has(n)) return true;
    // tolerancia: si la ciudad escrita coincide con el inicio de alguna oficial
    for (const c of set) {
      if (c === n) return true;
      // solo aceptar si coincide exactamente o es prefijo/suffix de una con sufijos comunes
      if (c === n + ' dc' || c === n + ' d.c' || c === n + ' d.c.') return true;
    }
    return false;
  };
})();
