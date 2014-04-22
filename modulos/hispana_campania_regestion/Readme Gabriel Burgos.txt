Módulos modificados
* hispana_listado_campanias
* hispana_interfaz_agente

Alter tables
MYSQL> alter table campania add tipo enum('ORIGINAL','REGESTION','DERIVADA') default 'ORIGINAL';
MYSQL> alter table campania add campania_origen int;

