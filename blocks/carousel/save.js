import { useBlockProps } from '@wordpress/block-editor';

export default function save({ attributes }) {
  const { images = [] } = attributes;
  return (
    <div {...useBlockProps.save({ className: 'wp-block-lejournaldesactus-carousel swiper' })}>
      <div className="swiper-wrapper">
        {images.map((img, i) => (
          <div className="swiper-slide" key={i}>
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
      {/* Les éléments navigation/pagination sont générés dynamiquement par frontend.js */}
    </div>
  );
}
