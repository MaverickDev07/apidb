## Servicio para las notas total de los 3 trimestres

### Parametros:
```json
{
    gestion: number,
    id_mat: number,
    id_prof: number,
    cod_curso: string,
    cod_nivel: string
}
```

```SQL
SELECT id_nota_trimestre total FROM nota_trimestre WHERE gestion=0 AND id_mat=0 AND id_prof=0 AND cod_curso='' AND cod_nivel='';
```

