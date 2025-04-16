import Edit from './edit';
import save from './save';
import './style.css';

wp.blocks.registerBlockType('lejournaldesactus/carousel', {
  edit: Edit,
  save,
});
