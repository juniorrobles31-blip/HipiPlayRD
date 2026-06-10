type HorsePreparationScreenProps = {
  selectedHorse: number;
  amount?: number;
  roundId?: number | string;
};

export function HorsePreparationScreen({
  selectedHorse
}: HorsePreparationScreenProps) {
  const safeHorse = Math.min(6, Math.max(1, Number(selectedHorse || 1)));
  const imageUrl = `/horse-prep/horse-${safeHorse}.png`;

  return (
    <section className="horse-preparation-fullscreen">
      <img
        className="horse-preparation-fullscreen-image"
        src={imageUrl}
        alt={`Caballo ${safeHorse} camino a los partidores`}
      />
    </section>
  );
}
