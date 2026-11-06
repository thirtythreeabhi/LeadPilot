import React, { useEffect, useRef, useState } from 'react';
import { cls } from '../utils.js';

export default function CustomSelect({ value, options, onChange, ariaLabel }) {
  const [open, setOpen] = useState(false);
  const [active, setActive] = useState(0);
  const root = useRef(null);
  const current = Math.max(0, options.findIndex(option => option.value === value));

  useEffect(() => {
    if (!open) return undefined;
    const dismiss = event => {
      if (!root.current?.contains(event.target)) setOpen(false);
    };
    document.addEventListener('mousedown', dismiss);
    return () => document.removeEventListener('mousedown', dismiss);
  }, [open]);

  function handleKeyDown(event) {
    if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
      event.preventDefault();
      if (!open) {
        setActive(current);
        setOpen(true);
      } else {
        const offset = event.key === 'ArrowDown' ? 1 : options.length - 1;
        setActive(index => (index + offset) % options.length);
      }
    } else if (event.key === 'Enter' || event.key === ' ') {
      event.preventDefault();
      if (open) {
        onChange(options[active].value);
        setOpen(false);
      } else {
        setActive(current);
        setOpen(true);
      }
    } else if (event.key === 'Escape') {
      setOpen(false);
    }
  }

  return (
    <div className={cls('custom-select', open && 'open')} ref={root}>
      <button
        type="button"
        className="custom-select-trigger"
        aria-label={ariaLabel}
        aria-haspopup="listbox"
        aria-expanded={open}
        onClick={() => { setActive(current); setOpen(value => !value); }}
        onKeyDown={handleKeyDown}
      >
        <span>{options[current]?.label}</span>
        <span className="custom-select-chevron" aria-hidden="true" />
      </button>
      {open && (
        <div className="custom-select-menu" role="listbox" aria-label={ariaLabel}>
          {options.map((option, index) => (
            <button
              type="button"
              role="option"
              aria-selected={option.value === value}
              key={option.value}
              className={cls('custom-select-option', option.value === value && 'selected', index === active && 'highlighted')}
              onMouseEnter={() => setActive(index)}
              onClick={() => { onChange(option.value); setOpen(false); }}
            >
              <span>{option.label}</span>
              {option.value === value && <span className="option-check" aria-hidden="true">✓</span>}
            </button>
          ))}
        </div>
      )}
    </div>
  );
}
