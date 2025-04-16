import { useBlockProps, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { Button, PanelBody, TextControl } from '@wordpress/components';
import { Fragment } from '@wordpress/element';

export default function Edit({ attributes, setAttributes }) {
  const { images = [] } = attributes;

  // Mettre à jour une image (url ou lien)
  const updateImage = (idx, key, value) => {
    const updated = images.map((img, i) => i === idx ? { ...img, [key]: value } : img);
    setAttributes({ images: updated });
  };

  // Supprimer une image
  const removeImage = (idx) => {
    setAttributes({ images: images.filter((_, i) => i !== idx) });
  };

  return (
    <div {...useBlockProps({ className: 'wp-block-lejournaldesactus-carousel swiper' })}>
      <InspectorControls>
        <PanelBody title="Images du carrousel">
          {images.length === 0 && <div>Aucune image sélectionnée.</div>}
          {images.map((img, i) => (
            <div key={i} style={{marginBottom:16,borderBottom:'1px solid #eee'}}>
              <TextControl
                label="Lien (optionnel)"
                value={img.link || ''}
                onChange={val => updateImage(i, 'link', val)}
                placeholder="https://..."
                __next40pxDefaultSize={true}
                __nextHasNoMarginBottom={true}
              />
              <Button isDestructive onClick={() => removeImage(i)} style={{marginTop:8}}>Supprimer</Button>
            </div>
          ))}
        </PanelBody>
      </InspectorControls>
      <MediaUploadCheck>
        <MediaUpload
          onSelect={mediaArr => {
            const imgs = (Array.isArray(mediaArr) ? mediaArr : [mediaArr]).map(m => ({ id: m.id, url: m.url, link: '' }));
            setAttributes({ images: [...images, ...imgs] });
          }}
          allowedTypes={['image']}
          multiple
          gallery
          value={images.map(img => img.id)}
          render={({ open }) => (
            <Button onClick={open} variant="primary" style={{marginBottom:16}}>
              {images.length === 0 ? 'Ajouter des images' : 'Ajouter plus d’images'}
            </Button>
          )}
        />
      </MediaUploadCheck>
      <div className="swiper-wrapper">
        {images.length === 0 && <div style={{textAlign:'center',padding:40,color:'#888'}}>Aucune image sélectionnée.</div>}
        {images.map((img, i) => (
          <div className="swiper-slide" key={i} style={{position:'relative'}}>
            {img.link ? (
              <a href={img.link} target="_blank" rel="noopener noreferrer">
                <img src={img.url} alt="" />
              </a>
            ) : (
              <img src={img.url} alt="" />
            )}
          </div>
        ))}
      </div>
    </div>
  );
}
