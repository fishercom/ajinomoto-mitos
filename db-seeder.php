<?php
/**
 * Script de Semilla (Seeder) para Poblar la Base de Datos con Contenido de Maquetación Real
 * Proyecto: Ajinomoto - Portal de Mitos
 */

define( 'WP_USE_THEMES', false );
require_once __DIR__ . '/wp-load.php';

wp_defer_term_counting( true );
wp_defer_comment_counting( true );

echo "=== INICIANDO CONFIGURACIÓN DE MITOS CON CONTENIDO DE MAQUETACIÓN ===\n";

// Helper para obtener ID de término
function get_term_id_by_slug( $slug, $taxonomy ) {
    $term = get_term_by( 'slug', $slug, $taxonomy );
    return $term ? $term->term_id : 0;
}

// 1. Obtener o crear Revisor
$revisor_title = 'Lic. Pamela Robles Valcárcel';
$existing_revisor = get_posts( array(
    'post_type'   => 'revisores',
    'title'       => $revisor_title,
    'post_status' => 'any',
    'numberposts' => 1
) );

if ( ! empty( $existing_revisor ) ) {
    $revisor_id = $existing_revisor[0]->ID;
} else {
    $revisor_id = wp_insert_post( array(
        'post_title'   => $revisor_title,
        'post_status'  => 'publish',
        'post_type'    => 'revisores',
        'post_content' => 'Pamela Robles es una destacada nutricionista con amplia experiencia en divulgación científica y educación alimentaria.'
    ) );
}

update_post_meta( $revisor_id, 'cargo_revisor', 'Nutricionista-Dietista — CNP 4853' );
update_post_meta( $revisor_id, 'resumen_revisor', 'Pamela Robles es una destacada nutricionista con amplia experiencia en divulgación científica y educación alimentaria para entidades de salud pública y privada.' );
update_post_meta( $revisor_id, 'formacion_academica', "<p>Licenciada en Nutrición y Dietética por la Universidad Nacional Mayor de San Marcos.</p><p>Especialista en Nutrición Clínica y Seguridad Alimentaria con estudios de postgrado en el extranjero.</p>" );
update_post_meta( $revisor_id, 'experiencia_profesional', "<p>Más de 10 años de experiencia asesorando a entidades de salud pública y privada en temas de nutrición, alimentación balanceada y seguridad.</p><p>Coordinadora de programas nacionales de salud y nutrición comunitaria en el Perú.</p>" );
update_post_meta( $revisor_id, 'premios_distinciones', "<p>Reconocimiento al Mérito Científico por el Colegio de Nutricionistas del Perú (2024).</p>" );
update_post_meta( $revisor_id, 'publicaciones', "<p>Coautora del manual 'Mitos y Realidades de la Alimentación Moderna' (2025).</p><p>Articulista habitual en revistas de divulgación de salud y nutrición en Latinoamérica.</p>" );


// Helper para registrar un mito
function update_or_insert_mito( $title, $tipo_slug, $cat_slug, $realidad, $sustento, $referencias = '', $revisor_id = 0, $nutricional = '', $palatabilidad = '' ) {
    $existing = get_posts( array(
        'post_type'   => 'mitos',
        'title'       => $title,
        'post_status' => 'any',
        'numberposts' => 1
    ) );

    if ( ! empty( $existing ) ) {
        $post_id = $existing[0]->ID;
        wp_update_post( array(
            'ID'           => $post_id,
            'post_content' => $sustento
        ) );
    } else {
        $post_id = wp_insert_post( array(
            'post_title'   => $title,
            'post_status'  => 'publish',
            'post_type'    => 'mitos',
            'post_content' => $sustento
        ) );
    }

    if ( is_wp_error( $post_id ) ) {
        echo "Error en mito '$title': " . $post_id->get_error_message() . "\n";
        return;
    }

    // Asociar taxonomías
    wp_set_object_terms( $post_id, $tipo_slug, 'tipo_mito' );
    wp_set_object_terms( $post_id, $cat_slug, 'categoria_mito' );

    // Metadatos
    update_post_meta( $post_id, 'subtitulo_mito', $title );
    update_post_meta( $post_id, 'realidad_corta', $realidad );
    update_post_meta( $post_id, 'sustento_cientifico', $sustento );
    update_post_meta( $post_id, 'perspectiva_nutricional', $nutricional );
    update_post_meta( $post_id, 'palatabilidad_digestion', $palatabilidad );
    update_post_meta( $post_id, 'referencias', $referencias );
    update_post_meta( $post_id, 'revisor', $revisor_id );

    echo "Mito configurado: '$title'\n";
}

// 2. Registrar Mitos del GMS (con el contenido exacto de la maquetación)
update_or_insert_mito(
    '¿El "síndrome del restaurante chino" es real?',
    'gms',
    'seguridad',
    'Gran parte de la comunidad científica considera que el "síndrome del restaurante chino" no es una condición médica claramente definida, sino más bien un concepto que se originó a partir de percepciones y asociaciones culturales, sin una base científica sólida.',
    '<p>El llamado "síndrome del restaurante chino" se mencionó por primera vez en 1968, cuando un médico publicó una carta en la revista científica New England Journal of Medicine. En ella describe una serie de síntomas, como dolor de cabeza, enrojecimiento o sensación de presión en el pecho, que algunas personas decían experimentar después de comer en restaurantes chinos. A partir de esta observación, se planteó la hipótesis de que uno de los posibles responsables podría ser el glutamato monosódico (GMS), un ingrediente utilizado para resaltar el sabor umami en muchos platos. Sin embargo, es importante tener en cuenta que esta idea surgió a partir de observaciones anecdóticas y no de estudios científicos controlados.</p><p>Con el tiempo, diversos investigadores han estudiado esta posible relación mediante ensayos clínicos controlados. En este tipo de estudios, los participantes consumen GMS o un placebo sin saber cuál están recibiendo, lo que permite evaluar de manera objetiva si el ingrediente provoca algún efecto. Este enfoque es clave para diferenciar entre efectos reales y aquellos que pueden estar influenciados por expectativas o percepciones previas.</p><p>Los resultados de estas investigaciones han sido consistentes: la mayoría de los estudios no ha encontrado evidencia concluyente de que el GMS cause los síntomas atribuidos al llamado síndrome. Es decir, no se ha podido establecer una relación directa entre su consumo y estas reacciones.</p><p>Además, investigaciones posteriores han mostrado que muchos de los síntomas descritos pueden estar relacionados con otros factores asociados a la experiencia de comer fuera de casa, como el contenido de sodio de los alimentos, el consumo de alcohol, el tamaño de las porciones o incluso el contexto social en el que se realiza la comida.</p>',
    'Geha, R. et al. (2000). Review of Alleged Reaction to Monosodium Glutamate and Outcome of a Multicenter Double-Blind Placebo-Controlled Study.',
    $revisor_id
);

update_or_insert_mito(
    '¿Existe una relación entre el GMS y el cáncer?',
    'gms',
    'seguridad',
    'No, según diversos estudios, el GMS no presenta efectos cancerígenos.',
    '<p>Durante décadas, el glutamato monosódico (GMS) ha estado rodeado de dudas y mitos sobre posibles efectos negativos en la salud, incluido el cáncer. Sin embargo, la evidencia científica disponible permite tener una mirada más clara sobre su impacto en el organismo. Diversos estudios toxicológicos y evaluaciones realizadas por organismos especializados han analizar cómo actúa este ingrediente en el cuerpo humano y animal, concluyendo que no presenta propiedades carcinogénicas cuando se consume en cantidades moderadas.</p><p>Una de las evaluaciones más importantes fue realizada por el Comité Mixto FAO/OMS de Expertos en Aditivos Alimentarios (JECFA), un panel internacional conformado por especialistas de la Organización de las Naciones Unidas para la Alimentación y la Agricultura (FAO) y la Organización Mundial de la Salud (OMS). Este comité revisó más de 200 estudios en animales y humanos y concluyó que el GMS no presenta efectos cancerígenos. A lo largo del tiempo, otras evaluaciones científicas han llegado a la misma conclusión, reafirmando la seguridad de su consumo.</p><p>Además, es importante comprender que el glutamato no es una sustancia extraña para el organismo, ya que el propio cuerpo lo produce de manera natural. Se trata de un aminoácido que se encuentra de forma natural en numerosos alimentos, incluso en la leche materna. Por ello, cuando consumimos GMS, el organismo lo procesa de la misma manera que el glutamato presente en alimentos naturales. Durante la digestión, la mayor parte es utilizada por las células del intestino como fuente de energía, lo que limita su paso al resto del organismo. En conjunto, toda esta evidencia acumulada a lo largo de los años ha llevado a que las principales autoridades sanitarias internacionales coincidan en que el GMS es seguro para el consumo humano cuando forma parte de una dieta normal y equilibrada.</p>',
    'Walker, R. & Lupien, J. (2000). The Safety Evaluation of Monosodium Glutamate. Journal of Nutrition.',
    $revisor_id
);

update_or_insert_mito(
    '¿Es seguro para niños y embarazadas?',
    'gms',
    'seguridad',
    'Sí, el GMS es seguro para la población general, incluidos niños y mujeres embarazadas, siempre que forme parte de una dieta equilibrada.',
    '<p>La seguridad del glutamato monosódico (GMS) ha sido evaluada durante décadas por organismos científicos y autoridades regulatorias de todo el mundo. Estas evaluaciones, basadas en múltiples estudios, han permitido determinar que el GMS es seguro para la población general, incluidos niños y mujeres embarazadas, siempre que forme parte de una dieta equilibrada.</p><p>Para entender por qué, es útil recordar que el glutamato es un aminoácido presente de forma natural en numerosos alimentos de consumo habitual, como tomates, quesos curados, carnes o setas. Además, el propio cuerpo humano produce glutamato como parte de su metabolismo normal. Cuando se consume, ya sea de forma natural o como GMS añadido, el organismo lo procesa principalmente en el intestino, donde se utiliza como fuente de energía para las células intestinales.</p><p>Debido a que el glutamato proveniente de los alimentos y el que se añade como GMS son químicamente iguales, el organismo los reconoce y procesa de la misma manera. Esto ayuda a explicar por qué su consumo no representa un riesgo adicional para grupos específicos de la población.</p><p>En conclusión, no hay evaluaciones científicas disponibles que indiquen riesgos particulares para niños o mujeres embarazadas cuando se consume dentro de una alimentación equilibrada.</p>',
    'Walker, R. & Lupien, J. (2000). The Safety Evaluation of Monosodium Glutamate. Journal of Nutrition.',
    $revisor_id
);

// 3. Registrar Mitos de la Cocina (contenido real)
update_or_insert_mito(
    'Echarle aceite al agua de los fideos evita que se peguen',
    'cocina',
    'tecnicas',
    'El aceite flota en el agua y no entra en contacto directo con los fideos durante la cocción. Lo que realmente evita que se peguen es usar suficiente agua y removerlos al inicio.',
    '<p>La física y la química culinaria demuestran que el aceite es menos denso que el agua y, por ende, flota en la superficie sin mezclarse ni recubrir la pasta durante el hervor. Lo que realmente evita que los fideos se peguen es la cocción en abundante agua (para diluir el almidón liberado) y la agitación mecánica frecuente durante los primeros 3 minutos, cuando el almidón se hidrata de forma rápida.</p><p>El uso innecesario de aceite solo añade calorías vacías al plato e impide que la salsa posterior se adhiera de forma correcta a la superficie de los fideos, restando sabor al resultado final.</p>',
    'Harold McGee, "On Food and Cooking: The Science and Lore of the Kitchen".',
    $revisor_id
);

update_or_insert_mito(
    'El horno de microondas destruye los nutrientes de los alimentos',
    'cocina',
    'salud',
    'Al contrario, debido a que el microondas cocina más rápido y usa menos agua, ayuda a preservar mejor las vitaminas termosensibles.',
    '<p>Los hornos de microondas cocinan los alimentos utilizando ondas de radio de alta frecuencia que agitan las moléculas de agua cargadas en los alimentos, calentándolas por fricción rápida y uniforme. Al requerir tiempos de cocción mucho más cortos y evitar sumergir los alimentos en grandes volúmenes de agua (donde se pierden la mayoría de vitaminas hidrosolubles como la C y el complejo B), el microondas conserva mejor los nutrientes que el hervido tradicional en olla.</p>',
    'Harvard Health Publishing, Harvard Medical School.',
    $revisor_id
);

update_or_insert_mito(
    'Lavar el pollo antes de cocinarlo es más higiénico',
    'cocina',
    'productos',
    'Lavar el pollo crudo bajo el grifo esparce bacterias dañinas por toda la cocina a través de microgotas de agua que salpican (efecto aerosol). La cocción adecuada es lo único que elimina las bacterias.',
    '<p>Lavar el pollo crudo bajo el grifo de la cocina aumenta de forma drástica el riesgo de contaminación cruzada debido a la dispersión de bacterias patógenas como Campylobacter y Salmonella a través de las microgotas de agua que salpican sobre utensilios, platos y superficies vecinas. La única manera real y efectiva de eliminar los patógenos del pollo es cocinarlo hasta que alcance una temperatura interna mínima segura de 74°C (165°F).</p>',
    'Centros para el Control y la Prevención de Enfermedades (CDC).',
    $revisor_id
);

update_or_insert_mito(
    'Comer fruta de noche engorda más que de día',
    'cocina',
    'nutricion',
    'La fruta aporta exactamente las mismas calorías independientemente de la hora del día en que se consuma. El aumento de peso depende del balance calórico diario total.',
    '<p>Las calorías y los nutrientes de una fruta no varían a lo largo de las 24 horas del día. El metabolismo humano no cambia la manera de metabolizar los azúcares naturales de la fruta según el reloj. Lo que determina el incremento o la pérdida de peso corporal es el balance calórico diario global frente al gasto de energía del individuo.</p><p>Además, por su alto contenido de agua y fibra soluble, consumir fruta de noche promueve una excelente saciedad frente a postres industriales pesados.</p>',
    'Sociedad Española para el Estudio de la Obesidad (SEEDO).',
    $revisor_id
);

wp_defer_term_counting( false );
wp_defer_comment_counting( false );

echo "=== MITOS ACTUALIZADOS CON ÉXITO ===\n";
